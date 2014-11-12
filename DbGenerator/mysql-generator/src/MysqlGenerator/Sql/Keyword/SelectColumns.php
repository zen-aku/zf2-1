<?php
namespace MysqlGenerator\Sql\Keyword;

use MysqlGenerator\Adapter\AdapterInterface;

class SelectColumns extends AbstractKeyword {
	
	const SQL_STAR = '*';
	
	/**
     * @var array
     */
    protected $columns = array();
	
	/**
     * @var string
     */
    protected $quotePrefix = '';
	
	/**
	 * @var boolean
	 */
	protected $hasPrefixColumns = true;
	
	/**
     * @param array $columns:
     *   array(*)
     *   array(value, ...)
     *     value can be strings or Expression objects
     *   array(string => value, ...)
     *     key string will be use as alias,
     *     value can be string or Expression objects
     * @param bool  $prefixColumnsWithTable
	 */
	public function __construct(array $columns = array(), $hasPrefixColumns = true){
		$this->columns = $columns;
		$this->hasPrefixColumns = (bool)$hasPrefixColumns;
    }
	
	/**
	 * @return boolean
	 */
	public function hasPrefixColumns() {
		return $this->hasPrefixColumns;
	}
	
	/**
	 * @param string $prefix
	 * @return SelectColumns
	 */
	public function setQuotePrefix($prefix) {
		$this->quotePrefix = $this->hasPrefixColumns ? $prefix : '';
		return $this;
	}
	
	/**
	 * @param AdapterInterface $adapter
	 */
	public function getSqlString(AdapterInterface $adapter = null) {	
		$columns = [];
		foreach ($this->columns as $keyAlias => $column) {
			if ($column === self::SQL_STAR) {
				$columns[] = $this->quotePrefix . self::SQL_STAR;
			}
			elseif ($column instanceof ExpressionInterface) {
				        
            } 
			else {
				$alias = is_string($keyAlias) ? ' AS ' . $this->quoteIdentifier($keyAlias) : '';
                $columns[] = $this->quotePrefix . $this->quoteIdentifier($column) . $alias;
            }	
		}
		return implode(', ', $columns);	
	}
	
	
	
	/*
	protected function processSelect(AdapterInterface $adapter = null, ParameterContainer $parameterContainer = null){
        $expr = 1;

        if ($this->table) {
            $table = $this->table;
            $schema = $alias = null;

            if (is_array($table)) {
                $alias = key($this->table);
                $table = current($this->table);
            }
            // create quoted table name to use in columns processing
            if ($table instanceof TableIdentifier) {
                list($table, $schema) = $table->getTableAndSchema();
            }
            if ($table instanceof Select) {
                $table = '(' . $this->processSubselect($table, $adapter, $parameterContainer) . ')';
            } else {
                $table = $this->quoteIdentifier($table);
            }
            if ($schema) {
                $table = $this->quoteIdentifier($schema) . '.' . $table;
            }
            if ($alias) {
                $fromTable = $this->quoteIdentifier($alias);
                $table = $this->renderTable($table, $fromTable);
            } else {
                $fromTable = $table;
            }
        } else {
            $fromTable = '';
        }
        if ($this->prefixColumnsWithTable) {
            $fromTable .= '.';
        } else {
            $fromTable = '';
        }

        // process table columns
        $columns = array();
        foreach ($this->columns as $columnIndexOrAs => $column) {

            $columnName = '';
            if ($column === self::SQL_STAR) {
                $columns[] = array($fromTable . self::SQL_STAR);
                continue;
            }
            if ($column instanceof ExpressionInterface) {
                $columnParts = $this->processExpression(
                    $column,
                    $adapter,
					true,
                    $this->processInfo['paramPrefix'] . ((is_string($columnIndexOrAs)) ? $columnIndexOrAs : 'column')
                );
                if ($parameterContainer) {
                    $parameterContainer->merge($columnParts->getParameterContainer());
                }
                $columnName .= $columnParts->getSql();
            } else {
                $columnName .= $fromTable . $this->quoteIdentifier($column);
            }

            // process As portion
            if (is_string($columnIndexOrAs)) {
                $columnAs = $this->quoteIdentifier($columnIndexOrAs);
            } elseif (stripos($columnName, ' as ') === false) {
                $columnAs = (is_string($column)) ? $this->quoteIdentifier($column) : 'Expression' . $expr++;
            }
            $columns[] = (isset($columnAs)) ? array($columnName, $columnAs) : array($columnName);
        }

        // process join columns
        foreach ($this->joins as $join) {
            foreach ($join['columns'] as $jKey => $jColumn) {
                $jColumns = array();
                if ($jColumn instanceof ExpressionInterface) {
                    $jColumnParts = $this->processExpression(
                        $jColumn,
                        $adapter,
						true,
                        $this->processInfo['paramPrefix'] . ((is_string($jKey)) ? $jKey : 'column')
                    );
                    if ($parameterContainer) {
                        $parameterContainer->merge($jColumnParts->getParameterContainer());
                    }
                    $jColumns[] = $jColumnParts->getSql();
                } else {
                    $name = (is_array($join['name'])) ? key($join['name']) : $name = $join['name'];
                    if ($name instanceof TableIdentifier) {
                        $name = ($name->hasSchema() ? $this->quoteIdentifier($name->getSchema()) . '.' : '') . $this->quoteIdentifier($name->getTable());
                    } else {
                        $name = $this->quoteIdentifier($name);
                    }
                    $jColumns[] = $name . '.' . $this->quoteIdentifierInFragment($jColumn);
                }
                if (is_string($jKey)) {
                    $jColumns[] = $this->quoteIdentifier($jKey);
                } elseif ($jColumn !== self::SQL_STAR) {
                    $jColumns[] = $this->quoteIdentifier($jColumn);
                }
                $columns[] = $jColumns;
            }
        }
        if ($this->quantifier) {
            if ($this->quantifier instanceof ExpressionInterface) {
                $quantifierParts = $this->processExpression($this->quantifier, $adapter, true, 'quantifier');
                if ($parameterContainer) {
                    $parameterContainer->merge($quantifierParts->getParameterContainer());
                }
                $quantifier = $quantifierParts->getSql();
            } else {
                $quantifier = $this->quantifier;
            }
        }
        if (!isset($table)) {
            return array($columns);
        } elseif (isset($quantifier)) {
            return array($quantifier, $columns, $table);
        } else {
            return array($columns, $table);
        }
    }
	*/
		
}
