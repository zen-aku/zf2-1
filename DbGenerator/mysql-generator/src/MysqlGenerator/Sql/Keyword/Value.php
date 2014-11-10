<?php

namespace MysqlGenerator\Sql\Keyword;

use MysqlGenerator\Adapter\AdapterInterface;
use MysqlGenerator\Adapter\StatementContainerInterface;
use MysqlGenerator\Sql\PreparableSqlInterface;
use MysqlGenerator\Sql\Expression;
use MysqlGenerator\Sql\Select;
use MysqlGenerator\Sql\Exception;

class Value extends AbstractKeyword implements PreparableSqlInterface {
	
	const ARRAY_ARRAY = 1;
	const ASSOC_ARRAY = 2;
	const ROW_ARRAY = 3;
	
	const KEYWORD = 'VALUES ';
	
	private static $bindIndex = 0;

	/**
     * @var array
     *      $values = []
     *      $values = [ [...], [...], [...], ... ]
     */
    protected $values = null;
	
	/**
	 * Возвращает тип массива self::ARRAY_ARRAY, self::ASSOC_ARRAY, self::ROW_ARRAY 
	 * или false, если тип массива неопределён (error)
	 * @param array $values
	 * @return int|false  
	 */
	public function checkValues(array $values) {
		$isArrayArray = false;
		$isRowArray = false;
		$isAssocArray = false;       
		foreach ($values as $key => $value) {
			if (is_array($value)) {
				if ($isRowArray || $isAssocArray) return false;				
				foreach ($value as $innerValue) {
					if (is_array($innerValue)) return false;
				}		
				$isArrayArray = 1;			
			}	
			elseif (is_int($key)) {
				if ($isArrayArray || $isAssocArray) return false;
				$isRowArray = 1;				
			}
            elseif (is_string($key)) {
                if ($isArrayArray || $isRowArray) return false;
                $isAssocArray = 1;
            }				
		}				
		if ($isArrayArray) return self::ARRAY_ARRAY; 
		if ($isRowArray) return self::ROW_ARRAY;
		if ($isAssocArray) return self::ASSOC_ARRAY;
	}	
	
	/**
	 * @var array $values [ [...], [...], [...], ... ]
	 */
	public function addValues(array $values) {	
		foreach ($values as $row) {
			$this->addRowValues($row);
		}
	}
	
	/**
	 * @var array $row [...]
	 */
	public function addRowValues(array $row) {	
		$this->values[] = $row;
	}

	/**
	 * @param AdapterInterface $adapter
	 * @return string  " VALUES (...), (...), ..."
	 * @throws InvalidArgumentException
	 */
	public function getSqlString(AdapterInterface $adapter){
            	
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
            return self::KEYWORD . implode(', ', $rowString);       	
        }      
        else {
            throw new Exception\InvalidArgumentException('values or select should be present');
        }       
    }
	
	
	/**
	 * Биндит массив $this->values в ParameterContainer объекта StatementContainerInterface $statementContainer
	 * и возвращает подготовленную строку ключа VALUES: " VALUES (':value1', ':value2', ...), (...), ..."
	 * @param AdapterInterface $adapter
	 * @param StatementContainerInterface $statementContainer
	 * @return string  " VALUES (':value1', ':value2', ...), (...), ..."
	 * @throws InvalidArgumentException
	 */
	public function prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer){
        
        $parameterContainer = $statementContainer->getParameterContainer();           
        $rowString = [];
        if (is_array($this->values)) {            
            foreach ($this->values as $row) {             
                $values = [];
                foreach ($row as $value) { 
                    if ($value instanceof Expression) {
                        $exprData = $this->processExpression($value, $adapter, true);
                        $values[] = $exprData->getSql();
                        $parameterContainer->merge($exprData->getParameterContainer());
                    }
                    elseif ($value instanceof Select) {
                        $values[] =  '(' . $value->prepareStatement($adapter, $statementContainer)->getSql() . ')';
                    }
                    else {         
                        $values[] = $bindName = ':value' . ++self::$bindIndex;                                 
                        $parameterContainer->offsetSet($bindName, $value);
                    }
                }          
                $rowString[] = '(' . implode(', ', $values) . ')';
            }      
        } 
		else {
            throw new Exception\InvalidArgumentException('values or select should be present');
        }
		return $statementContainer->setSql(self::KEYWORD . implode(', ', $rowString));
    }
	
		
}

