<?php

namespace MysqlGenerator\Sql;

use MysqlGenerator\Adapter\ParameterContainer;
use MysqlGenerator\Adapter\StatementContainerInterface;
use MysqlGenerator\Adapter\AdapterInterface;

class Insert extends AbstractSql implements SqlInterface, PreparableSqlInterface {
	
    /**
     * @var string|TableIdentifier
     */
    protected $table = null;
	
	/**
	 * @var array 
	 */
    protected $columns = array();

    /**
     * @param  null|string|TableIdentifier $table
     */
    public function __construct($table = null){
        $this->table = $table;
    }

    /**
     * Create INTO clause
     * @param  string|TableIdentifier $table
     * @return Insert
     */
    public function into($table){
        $this->table = $table;
        return $this;
    }

    /**
     * Specify columns
     * @param  array $columns
     * @return Insert
     */
    public function columns(array $columns){
        $this->columns = $columns;
        return $this;
    }

    
	//////////////////////////////////////////////////////////////////////
	
    /**
     * @const
     */
    const SPECIFICATION_INSERT = 'insert';
    
    /**
     * %1$s - <schema.table>
     * %2$s - <columns> ()
     * %3$s - VALUES (),(),...
     *      - SELECT ...
     * @var array Specification array
     */
    protected $specifications = array(
        self::SPECIFICATION_INSERT => 'INSERT INTO %1$s %2$s %3$s',
    );
    
	/**
     * @var array|Select 
     *      $values = []
     *      $values = [ [...], [...], [...], ... ]
     *      $values = ['col1'=>'val1', 'col2'=>'val2', ...]
     *      $values = new Select()
     */
    protected $values = null;
       	
    /**
     * Для задания выражения INSERT INTO tbl_name() SELECT...
	 * c предварительным или без заданием колонок столбцов через Insert::columns()
     * @param Select $select
     * @return self
     */
    public function select( Select $select ){
        $this->values = $select;
        return $this;
    }
	
	/**
	 * Для задания значений VALUES() выражения INSERT INTO tbl_name() VALUES():
	 * 1. без предварительного задания столбцов через Insert::columns() ($this->columns = пустой array())
	 *		INSERT INTO tbl_name VALUES(null,2,3);
	 *		$values = [null,2,3]
     *		INSERT INTO tbl_name VALUES(null,2,3),(null,5,6),(null,8,9);
	 *		$values = [[null,2,3], [null,5,6], [null,8,9]]
	 * 2. c предварительным заданием столбцов через Insert::columns(['a','b','c'])
	 *		INSERT INTO tbl_name(a,b,c) VALUES(1,2,3);
	 *		$values = [1,2,3]
	 *		INSERT INTO tbl_name(a,b,c) VALUES(1,2,3),(4,5,6),(7,8,9);
	 *		$values = [[null,2,3], [null,5,6], [null,8,9]]
     * 3. c указанием столбцов вместе со значениями как в INSERT INTO tbl_name SET a=1, b=2, c=3
     *      INSERT INTO tbl_name(a,b,c) VALUES(1,2,3);
	 *		$values = ['a' => 1, 'b' => 2, 'c' => 3] 
     * 4. Для задания выражения INSERT INTO tbl_name() SELECT...
     *      $values = new Select();
     *      INSERT INTO `users` (`name`, `age`) SELECT `name` , `age` FROM `users` WHERE id = 3
	 * @param  array|Select $values
	 * @return $this
	 */
	public function values( $values ) {
        
        if (!is_array($values) && !$values instanceof Select) {
            throw new Exception\InvalidArgumentException('values() expects an array of values or Zend\Db\Sql\Select instance');
        }       
        if ($values instanceof Select) {
            $this->values = $values;
            return $this;
        }		     
		$errorValues = false;
		$isOneDimensArrayValues = false;
		$isTwoDimensArrayValues = false;
        
        // check argument $values
		foreach ( $values as $key => $value ) {
			if ( is_array($value) ) {
				if ($isOneDimensArrayValues) {
					$errorValues = true;
					break;
				}
				foreach ($value as $innerValue) {
					if ( is_array($innerValue) ) {
						$errorValues = true;
						break 2;
					}
				}		
				$isTwoDimensArrayValues = true;			
			}	
			elseif (is_int($key)) {
				if ($isTwoDimensArrayValues) {
					$errorValues = true;
					break;
				}
				$isOneDimensArrayValues = true;				
			}
            elseif (is_string($key)) {
                if ($isOneDimensArrayValues || $isTwoDimensArrayValues) {
                    $errorValues = true;
					break;
                }
                return $this->set($values);
            }
			else {
				$errorValues = true;
				break;		
			}		
		}		
		if ($errorValues) {
			throw new Exception\InvalidArgumentException(
				'В MysqlGenerator\Sql\Insert::values($values) неправильно задан аргумент $values. '
			);
		}
		
		if ($isOneDimensArrayValues) {			
			$this->values[] = $values;
		}	
		elseif ($isTwoDimensArrayValues) {
			foreach ($values as $value) {
				$this->values[] = $value;
			}
		}	
		return $this;	
	}
	
	/**
	 * Для задания выражения вида INSERT INTO tbl_name SET a=1, b=2, c=3
	 * $values = ['a'=> 1, 'b'=> 2, 'c'=> 3]
	 * В Insert::getSqlString() преобразуется в шаблон : INSERT INTO tbl_name (a,b,c) VALUES(1,2,3)
	 * @param  array $values
	 * @return $this
	 */
	public function set( array $values ) {	        
        $this->values = null;       
        $this->columns = [];
        
        foreach ($values as $key => $value) {
            if (!is_string($key)) {
                throw new Exception\InvalidArgumentException(
                    'В MysqlGenerator\Sql\Insert::set($values) неправильно задан аргумент $values. '
                    .'Аргумент $values может быть только одномерным accоциативным массивом.'
                );
            } 
            $columns[] = $key;
            $data[] = $value;          
        }
        $this->columns = $columns;
        $this->values[] = $data;
        return $this;
    }   
    
    /**
     * Get SQL string for this statement:
     *  "INSERT INTO tbl_name VALUES(null,2,3),(null,5,6),(null,8,9),..."
     *  "INSERT INTO tbl_name(a,b,c) VALUES(1,2,3),(4,5,6),(7,8,9),..."
     *  "INSERT INTO tbl_name (a,b) VALUES((SELECT...), (SELECT...)), ((SELECT...), (SELECT...)), ..."
     *  "INSERT INTO tbl_name(a,b,c) SELECT ...
     * @param AdapterInterface $adapter
     * @return string
     */
    public function getSqlString(AdapterInterface $adapter){
        
        // $table = " `schema`.`table` "
        if ($table instanceof TableIdentifier) {
            $table = $adapter->quoteIdentifier($table->getSchema()) . '.' 
                . $adapter->quoteIdentifier($table->getTable());
        }
        else {
            $table = $adapter->quoteIdentifier($this->table);
        } 
        
        // $columns = " (`column1`, `column2`, `column3` ...)" 
        $columns = '';
        if (count($this->columns) > 0) {
            $columns = '(' . implode( ', ', array_map(array($adapter, 'quoteIdentifier'), $this->columns) ) . ')';
        }    
        
        // $sqlString = " VALUES (...), (...), ..." или " SELECT ... "
        $rowString = [];
        if ( is_array($this->values) ) {
            foreach ($this->values as $row) {
                $values = [];
                foreach ($row as $value) {             
                    if ($value instanceof Expression) {
                        $values[] = $this->processExpression($value, $adapter)->getSql();
                    }
                    elseif ($value instanceof Select) {
                        $values[] = '(' . $value->getSqlString($adapter) . ')';
                    } 
                    elseif ($value === null) {
                        $values[] = 'NULL';
                    } 
                    else {
                        $values[] = $adapter->quoteValue($value);
                    }                     
                }
                $rowString[] = '(' . implode(', ', $values) . ')';        
            }
            $valuesString = 'VALUES ' . implode(', ', $rowString);       	
        }
        elseif ($this->values instanceof Select) {
            $valuesString = $this->values->getSqlString($adapter);        			
        }
        else {
            throw new Exception\InvalidArgumentException('values or select should be present');
        }
        
        // sqlString = "INSERT INTO tbl_name(a,b,c) VALUES(1,2,3),(4,5,6),(7,8,9),..." или "INSERT INTO tbl_name(a,b,c) SELECT ..."
        return sprintf(
            $this->specifications[static::SPECIFICATION_INSERT],
            $table,
            $columns,
            $valuesString
        );
    }

    				
	////////////////////////////////////////////////////////////////////////////////////////////////
	

    /**
     * Get raw state
     * @param string $key
     * @return mixed
     */
//???
    public function getRawState($key = null){
        $rawState = array(
            'table' => $this->table,
            'columns' => $this->columns,
            'values' => $this->values
        );
        return (isset($key) && array_key_exists($key, $rawState)) ? $rawState[$key] : $rawState;
    }

    /**
     * Prepare statement
     * @param  AdapterInterface $adapter
     * @param  StatementContainerInterface $statementContainer
     * @return void
     */
//???
    public function prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer){
        $parameterContainer = $statementContainer->getParameterContainer();

        if (!$parameterContainer instanceof ParameterContainer) {
            $parameterContainer = new ParameterContainer();
            $statementContainer->setParameterContainer($parameterContainer);
        }
        $table = $this->table;
        $schema = null;

        // create quoted table name to use in insert processing
        if ($table instanceof TableIdentifier) {
            list($table, $schema) = $table->getTableAndSchema();
        }
        $table = $adapter->quoteIdentifier($table);

        if ($schema) {
            $table = $adapter->quoteIdentifier($schema) . '.' . $table;
        }
        $columns = array();
        $values  = array();

        if (is_array($this->values)) {
            foreach ($this->columns as $cIndex => $column) {
                $columns[$cIndex] = $adapter->quoteIdentifier($column);
				
                if ( isset($this->values[$cIndex]) && $this->values[$cIndex] instanceof Expression ) {
                    $exprData = $this->processExpression($this->values[$cIndex], $adapter, true);
                    $values[$cIndex] = $exprData->getSql();
                    $parameterContainer->merge($exprData->getParameterContainer());
                } 
				else {
                    $values[$cIndex] = $adapter->formatParameterName($column);
                    if (isset($this->values[$cIndex])) {
                        $parameterContainer->offsetSet($column, $this->values[$cIndex]);
                    } 
					else {
                        $parameterContainer->offsetSet($column, null);
                    }
                }
            }
            $sql = sprintf(
                $this->specifications[static::SPECIFICATION_INSERT],
                $table,
                implode(', ', $columns),
                implode(', ', $values)
            );		
        } 
		elseif ($this->values instanceof Select) {
            $this->values->prepareStatement($adapter, $statementContainer);

            $columns = array_map(array($adapter, 'quoteIdentifier'), $this->columns);
            $columns = implode(', ', $columns);

            $sql = sprintf(
                $this->specifications[static::SPECIFICATION_SELECT],
                $table,
                $columns ? "($columns)" : "",
                $statementContainer->getSql()
            );
        } 
		else {
            throw new Exception\InvalidArgumentException('values or select should be present');
        }
		
        $statementContainer->setSql($sql);
    }

    /**
     * Overloading: variable setting
     * Proxies to values, using VALUES_MERGE strategy
     * @param  string $name
     * @param  mixed $value
     * @return Insert
     */
//Убрать вообще???
    public function __set($name, $value){
        $values = array($name => $value);
        $this->values($values, self::VALUES_MERGE);
        return $this;
    }

    /**
     * Overloading: variable unset
     * Proxies to values and columns
     * @param  string $name
     * @throws Exception\InvalidArgumentException
     * @return void
     */
//Убрать вообще???
    public function __unset($name){
        if (($position = array_search($name, $this->columns)) === false) {
            throw new Exception\InvalidArgumentException('The key ' . $name . ' was not found in this objects column list');
        }
        unset($this->columns[$position]);
        if (is_array($this->values)) {
            unset($this->values[$position]);
        }
    }

    /**
     * Overloading: variable isset
     * Proxies to columns; does a column of that name exist?
     * @param  string $name
     * @return bool
     */
//Убрать вообще???
    public function __isset($name){
        return in_array($name, $this->columns);
    }

    /**
     * Overloading: variable retrieval
     * Retrieves value by column name
     * @param  string $name
     * @throws Exception\InvalidArgumentException
     * @return mixed
     */
//Убрать вообще???
    public function __get($name){
        if (!is_array($this->values)) {
            return null;
        }
        if (($position = array_search($name, $this->columns)) === false) {
            throw new Exception\InvalidArgumentException('The key ' . $name . ' was not found in this objects column list');
        }
        return $this->values[$position];
    }
}
