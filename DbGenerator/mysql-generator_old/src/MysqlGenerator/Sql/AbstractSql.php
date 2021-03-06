<?php

namespace MysqlGenerator\Sql;

use MysqlGenerator\Adapter\ParameterContainer;
use MysqlGenerator\Adapter\StatementContainer;
use MysqlGenerator\Adapter\AdapterInterface;

abstract class AbstractSql {
	
    /**
     * @var array
     */
    protected $specifications = array();

    /**
     * @var string
     */
    protected $processInfo = array('paramPrefix' => '', 'subselectCount' => 0);

    /**
     * @var array
     */
    protected $instanceParameterIndex = array();

	/**
	 * Из объекта ExpressionInterface $expression (Expression или Predicate объект) получить объект StatementContainer, 
	 * в котором будут храниться подготовленная ($bind = true) или чистая ($bind = false) строка запроса 
	 * и свойство-объект ParameterContainer с параметрами для подготовленной строки.
     * @staticvar int $runtimeExpressionPrefix
     * @param ExpressionInterface $expression
     * @param AdapterInterface  $adapter
	 * @param boolean $bind : 
	 *		true - подготавливать bind-параметры для statement из $expression 
	 *		false - передавать строку запроса из $expression в statement без bind-параметров
     * @param type $namedParameterPrefix
     * @return StatementContainer
     * @throws Exception\RuntimeException
     */
	protected function processExpression (
		ExpressionInterface $expression, 
		AdapterInterface $adapter, 
		$bind = false, 
		$namedParameterPrefix = null 
	) {
		// initialize variables
		$sql = '';
        // static counter for the number of times this method was invoked across the PHP runtime
        static $runtimeExpressionPrefix = 0;

        if ($bind && ((!is_string($namedParameterPrefix) || $namedParameterPrefix == ''))) {
            $namedParameterPrefix = sprintf('expr%04dParam', ++$runtimeExpressionPrefix);
        }
        if (!isset($this->instanceParameterIndex[$namedParameterPrefix])) {
            $this->instanceParameterIndex[$namedParameterPrefix] = 1;
        }
        $expressionParamIndex = &$this->instanceParameterIndex[$namedParameterPrefix];		
		$statementContainer = new StatementContainer();
        $parameterContainer = $statementContainer->getParameterContainer();
		
		$parts = $expression->getExpressionData();		
        foreach ($parts as $part) {
            // if it is a string, simply tack it onto the return sql "specification" string
            if (is_string($part)) {
                $sql .= $part;
                continue;
            }
            if (!is_array($part)) {
                throw new Exception\RuntimeException('Elements returned from getExpressionData() array must be a string or array.');
            }
            // process values and types (the middle and last position of the expression data)
            $values = $part[1];
            $types = (isset($part[2])) ? $part[2] : array();
			
            foreach ($values as $vIndex => $value) {				
				if ( isset($types[$vIndex]) ) {
					switch ( $types[$vIndex] ) {						
						case ExpressionInterface::TYPE_IDENTIFIER : {
							$values[$vIndex] = $this->quoteIdentifierInFragment($value);
							break;
						}
						case ExpressionInterface::TYPE_LITERAL : {
							$values[$vIndex] = $value;
							break;
						}
						case ExpressionInterface::TYPE_VALUE : {
							if ($value instanceof Select) {
								// process sub-select
								if ($bind) {
									$values[$vIndex] = '(' . $this->processSubSelect($value, $adapter, $parameterContainer) . ')';
								} else {
									$values[$vIndex] = '(' . $value->getSqlString($adapter) . ')';
								}
							}
							elseif ($value instanceof ExpressionInterface) {
								// recursive call to satisfy nested expressions
								$innerStatementContainer = $this->processExpression($value, $adapter, $bind, $namedParameterPrefix . $vIndex . 'subpart');
								$values[$vIndex] = $innerStatementContainer->getSql();
								if ($bind) {
									$parameterContainer->merge($innerStatementContainer->getParameterContainer());
								}					
							}
							else {
								// if prepareType is set, it means that this particular value must be passed back to the statement in a way it can be used as a placeholder value
								if ($bind) {
									$name = $namedParameterPrefix . $expressionParamIndex++;
									$parameterContainer->offsetSet($name, $value);
									$values[$vIndex] = $adapter->formatParameterName($name);
									continue; 
								}
								// if not a preparable statement, simply quote the value and move on
								$values[$vIndex] = $adapter->quoteValue($value);							
							}
						}
					}
				}		
            }			
            // after looping the values, interpolate them into the sql string (they might be placeholder names, or values)
            $sql .= vsprintf($part[0], $values);
        }
        $statementContainer->setSql($sql);
        return $statementContainer;
    }
	
	/**
	 * @param \MysqlGenerator\Sql\Select $subselect
	 * @param AdapterInterface $adapter
	 * @param boolean $bind : 
	 *		true - подготавливать bind-параметры для statement из $expression 
	 *		false - передавать строку запроса из $expression в statement без bind-параметров
	 * @param ParameterContainer $parameterContainer
	 * @return type
	 */
    protected function processSubSelect(
		Select $subselect, 
		AdapterInterface $adapter, 
		ParameterContainer $parameterContainer
	) {		
		
		$stmtContainer = new StatementContainer;

		// Track subselect prefix and count for parameters
		$this->processInfo['subselectCount']++;
		$subselect->processInfo['subselectCount'] = $this->processInfo['subselectCount'];
		$subselect->processInfo['paramPrefix'] = 'subselect' . $subselect->processInfo['subselectCount'];
		$subselect->prepareStatement($adapter, $stmtContainer);

		// copy count
		$this->processInfo['subselectCount'] = $subselect->processInfo['subselectCount'];
		$parameterContainer->merge($stmtContainer->getParameterContainer()->getNamedArray());
        
        return $stmtContainer->getSql();
    }
	
    /**
     * @param $specifications
     * @param $parameters
     * @return string
     * @throws Exception\RuntimeException
     */
    protected function createSqlFromSpecificationAndParameters($specifications, $parameters){
        if (is_string($specifications)) {
            return vsprintf($specifications, $parameters);
        }		
        $parametersCount = count($parameters);
        foreach ($specifications as $specificationString => $paramSpecs) {
            if ($parametersCount == count($paramSpecs)) {
                break;
            }
            unset($specificationString, $paramSpecs);
        }		
        if (!isset($specificationString)) {
            throw new Exception\RuntimeException(
                'A number of parameters was found that is not supported by this specification'
            );
        }
		
        $topParameters = array();
        foreach ($parameters as $position => $paramsForPosition) {	
			
            if (isset($paramSpecs[$position]['combinedby'])) {
                $multiParamValues = array();
                foreach ($paramsForPosition as $multiParamsForPosition) {
                    $ppCount = count($multiParamsForPosition);
                    if (!isset($paramSpecs[$position][$ppCount])) {
                        throw new Exception\RuntimeException('A number of parameters (' . $ppCount . ') was found that is not supported by this specification');
                    }
                    $multiParamValues[] = vsprintf($paramSpecs[$position][$ppCount], $multiParamsForPosition);
                }
                $topParameters[] = implode($paramSpecs[$position]['combinedby'], $multiParamValues);
            }
			elseif ($paramSpecs[$position] !== null) {
                $ppCount = count($paramsForPosition);
                if (!isset($paramSpecs[$position][$ppCount])) {
                    throw new Exception\RuntimeException('A number of parameters (' . $ppCount . ') was found that is not supported by this specification');
                }
                $topParameters[] = vsprintf($paramSpecs[$position][$ppCount], $paramsForPosition);
            } 
			else {
                $topParameters[] = $paramsForPosition;
            }
        }
        return vsprintf($specificationString, $topParameters);
    }
    
    /**
     * Quote identifier
     * @param  string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier) {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
    
    //////////////////////////////////
    /**
     * Quote identifier in fragment
     * @param  string $identifier
     * @param  array $safeWords
     * @return string
     */
    public function quoteIdentifierInFragment($identifier, array $safeWords = array()) {
        // regex taken from @link http://dev.mysql.com/doc/refman/5.0/en/identifiers.html
        $parts = preg_split('#([^0-9,a-z,A-Z$_])#', $identifier, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if ($safeWords) {
            $safeWords = array_flip($safeWords);
            $safeWords = array_change_key_case($safeWords, CASE_LOWER);
        }
        foreach ($parts as $i => $part) {
            if ($safeWords && isset($safeWords[strtolower($part)])) {
                continue;
            }
            switch ($part) {
                case ' ':
                case '.':
                case '*':
                case 'AS':
                case 'As':
                case 'aS':
                case 'as':
                    break;
                default:
                    $parts[$i] = '`' . str_replace('`', '``', $part) . '`';
            }
        }
        return implode('', $parts);
    }
    
    /**
     * @return string  "`schema`.`table`"
     */
    public function getQuoteSchemaTable() {
        if ($this->table instanceof \MysqlGenerator\Sql\TableIdentifier) {
            return $this->quoteIdentifier($this->table->getSchema()) . '.' 
                . $this->quoteIdentifier($this->table->getTable());
        }
        else {
            return $this->quoteIdentifier($this->table);
        } 
    }
    
    /**
     * @param array $list [elemen1, elemen2, ...]
     * @return string  "`elemen1`, `elemen2`, ..."
     */
    public function getQuoteList( array $list ) {
       return implode( ', ', array_map( array($this, 'quoteIdentifier'), $list) );        
    }
    

}
