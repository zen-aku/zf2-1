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
     * @staticvar int $runtimeExpressionPrefix
     * @param \MysqlGenerator\Sql\ExpressionInterface $expression
     * @param AdapterInterface  $adapter
     * @param type $namedParameterPrefix
     * @return StatementContainer
     * @throws Exception\RuntimeException
     */
    protected function processExpression( ExpressionInterface $expression, AdapterInterface $adapter = null, $namedParameterPrefix = null ) {
        // static counter for the number of times this method was invoked across the PHP runtime
        static $runtimeExpressionPrefix = 0;

        if ($adapter && ((!is_string($namedParameterPrefix) || $namedParameterPrefix == ''))) {
            $namedParameterPrefix = sprintf('expr%04dParam', ++$runtimeExpressionPrefix);
        }

        $sql = '';
        $statementContainer = new StatementContainer;
        $parameterContainer = $statementContainer->getParameterContainer();

        // initialize variables
        $parts = $expression->getExpressionData();

        if (!isset($this->instanceParameterIndex[$namedParameterPrefix])) {
            $this->instanceParameterIndex[$namedParameterPrefix] = 1;
        }
        $expressionParamIndex = &$this->instanceParameterIndex[$namedParameterPrefix];

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
                if (isset($types[$vIndex]) && $types[$vIndex] == ExpressionInterface::TYPE_IDENTIFIER) {
                    $values[$vIndex] = $adapter->quoteIdentifierInFragment($value);
                } elseif (isset($types[$vIndex]) && $types[$vIndex] == ExpressionInterface::TYPE_VALUE && $value instanceof Select) {
                    // process sub-select              
                    $values[$vIndex] = '(' . $this->processSubSelect($value, $adapter, $parameterContainer) . ')';                   
                } elseif (isset($types[$vIndex]) && $types[$vIndex] == ExpressionInterface::TYPE_VALUE && $value instanceof ExpressionInterface) {
                    // recursive call to satisfy nested expressions
                    $innerStatementContainer = $this->processExpression($value, $adapter, $namedParameterPrefix . $vIndex . 'subpart');
                    $values[$vIndex] = $innerStatementContainer->getSql();
                    if ($adapter) {
                        $parameterContainer->merge($innerStatementContainer->getParameterContainer());
                    }
                } elseif (isset($types[$vIndex]) && $types[$vIndex] == ExpressionInterface::TYPE_VALUE) {

                    // if prepareType is set, it means that this particular value must be
                    // passed back to the statement in a way it can be used as a placeholder value
                    if ($adapter) {
                        $name = $namedParameterPrefix . $expressionParamIndex++;
                        $parameterContainer->offsetSet($name, $value);
                        $values[$vIndex] = $adapter->formatParameterName($name);
                        continue;
                    }

                    // if not a preparable statement, simply quote the value and move on
                    $values[$vIndex] = $adapter->quoteValue($value);
                } elseif (isset($types[$vIndex]) && $types[$vIndex] == ExpressionInterface::TYPE_LITERAL) {
                    $values[$vIndex] = $value;
                }
            }

            // after looping the values, interpolate them into the sql string (they might be placeholder names, or values)
            $sql .= vsprintf($part[0], $values);
        }
        $statementContainer->setSql($sql);
        return $statementContainer;
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
	 * @param \MysqlGenerator\Sql\Select $subselect
	 * @param AdapterInterface $adapter
	 * @param ParameterContainer $parameterContainer
	 * @return type
	 */
    protected function processSubSelect(Select $subselect, AdapterInterface $adapter = null, ParameterContainer $parameterContainer = null) {
        if ($adapter) {
            $stmtContainer = new StatementContainer;

            // Track subselect prefix and count for parameters
            $this->processInfo['subselectCount']++;
            $subselect->processInfo['subselectCount'] = $this->processInfo['subselectCount'];
            $subselect->processInfo['paramPrefix'] = 'subselect' . $subselect->processInfo['subselectCount'];
			$subselect->prepareStatement(new \MysqlGenerator\Adapter\Adapter($adapter), $stmtContainer);
         
            // copy count
            $this->processInfo['subselectCount'] = $subselect->processInfo['subselectCount'];
            $parameterContainer->merge($stmtContainer->getParameterContainer()->getNamedArray());
            $sql = $stmtContainer->getSql();
        } 
		else {         
            $sql = $subselect->getSqlString($adapter);         
        }
        return $sql;
    }
}
