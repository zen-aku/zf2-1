<?php

namespace MysqlGenerator\Sql\Keyword;

use MysqlGenerator\Adapter\AdapterInterface;

class Table extends AbstractKeyword {

	/**
     * @var string
     */
    protected $table;
	
	/**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $schema;
	
	/**
     * @var string
     */
	protected $sqlStringPartition = '';

    /**
     * @param string|array $table : 'table', ['table'], ['alias' => 'table']
     * @param string $schema
     */
    public function __construct( $table, $schema = null ){
		$this->schema = $schema;
		
		if (is_string($table)) {
			$this->table = $table;
		}
		elseif (is_array($table)) {
			if (is_string($this->alias = key($table)) && count($table) === 1) {
				$this->table = current($table);
			}
			else {
				throw new Exception\InvalidArgumentException('expects $table as an array is a single element associative array');
			}
		}
		else {
			throw new Exception\InvalidArgumentException('$table must be a string or array');
		}	
    }
	
	/**
	 * @param string $table
	 * @return From
	 */
	public function setTable($table) {
		$this->table = $table;	
		return $this;
	}
	
	/**
	 * @param string $alias
	 * @return From
	 */
	public function setAlias($alias) {
		$this->alias = $alias;	
		return $this;
	}
	
	/** 
	 * @param string $schema
	 * @return From
	 */
	public function setSchema($schema) {
		$this->schema = $schema;
		return $this;
	}
	
	/**
	 * "PARTITION (`p1`, `p2`)"
	 * @param Partition $partition 
	 */
	public function setPartition(Partition $partition) {
		$this->sqlStringPartition = ' ' . $partition->getSqlString();
		return $this;
	}
	
	/**
	 * @return string  "`schema`.`table`"
	 */
	public function getQuoteSchemaTable() {
		$schema = $this->schema ? $this->quoteIdentifier($this->schema) . '.' : '';
		return $schema . $this->quoteIdentifier($this->table);
	}
		
	/**
	 * @return string  "`alias`." или  "`schema`.`table`."
	 */
	public function getQuotePrefix() {	
		return $this->alias ?  $this->quoteIdentifier($this->alias) . '.' : 		
		$this->getQuoteSchemaTable() . '.';	
	}
				
	/**
	 * @return string  " `schema`.`table`" или " `schema`.`table` AS `alias` "
	 */	
	public function getSqlString(AdapterInterface $adapter = null) {
		$alias = $this->alias ?  ' AS ' . $this->quoteIdentifier($this->alias) : '';
		return $this->getQuoteSchemaTable() . $this->sqlStringPartition . $alias;	
	}
	
}
