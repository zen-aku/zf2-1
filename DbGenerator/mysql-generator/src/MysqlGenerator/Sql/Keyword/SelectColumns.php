<?php
namespace MysqlGenerator\Sql\Keyword;

use MysqlGenerator\Adapter\AdapterInterface;

class SelectColumns extends AbstractKeyword {
	
	/**
     * @var array
     */
    protected $columns = array();
	
	/**
	 * @var boolean
	 */
	protected $prefixColumnsWithTable = true;
	
	/**
	 * @var From
	 */
	protected $table = null;
	
	/**
	 * @var JoinContainer
	 */
	protected $joinContainer = null;


	/**
	 * /**
     * Specify columns from which to select
     * Possible valid states:
     *   array(*)
     *   array(value, ...)
     *     value can be strings or Expression objects
     *   array(string => value, ...)
     *     key string will be use as alias,
     *     value can be string or Expression objects
     *
     * @param  array $columns
     * @param  bool  $prefixColumnsWithTable
	 * @param array $columns
	 */
	public function __construct(array $columns = array(), $prefixColumnsWithTable = true){
		$this->columns = $columns;
		$this->prefixColumnsWithTable = (bool)$prefixColumnsWithTable;
    }
	
	/** 
	 * @param From $table
	 * @return SelectColumns
	 */
	public function setTable(From $table) {
		$this->table = $table;
		return $this;
	}
	
	/** 
	 * @param From $table
	 * @return SelectColumns
	 */
	public function setJoinContainer(JoinContainer $joinContainer) {
		$this->joinContainer = $joinContainer;
		return $this;
	}
	
	// ??? getPrefix(From $table)
	public function getPrefix() {		
		if ($this->table && $this->prefixColumnsWithTable) {			
			if ($this->table->getAlias()) {
				$fromTable = $this->quoteIdentifier($alias);
			} 
			else {			
				$schema = $this->table->getSchema() ? $this->quoteIdentifier($this->table->getSchema()) : '';
				$fromTable = $schema . '.' . $this->quoteIdentifier($this->table->getTable());
			}
			return  '.' . $fromTable;
		}
		else {
			return '';
		}
	}

		/**
	 * @param AdapterInterface $adapter
	 */
	public function getSqlString(AdapterInterface $adapter = null) {
		
	}
	
	
	
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
	
	
	
	
	
}
