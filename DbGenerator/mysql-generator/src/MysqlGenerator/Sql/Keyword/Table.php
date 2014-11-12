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
    protected $schema;

    /**
     * @param string $table
     * @param string $schema
     */
    public function __construct( $table, $schema = null ){
        $this->table = $table;
        $this->schema = $schema;
    }
	
	/**
	 * @param string $table
	 * @return Table
	 */
	public function setTable($table) {
		$this->table = $table;
		return $this;
	}
	
	/** 
	 * @param string $schema
	 * @return Table
	 */
	public function setSchema($schema) {
		$this->schema = $schema;
		return $this;
	}
	
	/**
	 * @return string " `schema`.`table` "
	 */
	public function getSqlString(AdapterInterface $adapter = null) {			
		return ($this->schema) ? 
			$this->quoteIdentifier($this->schema) . '.' . $this->quoteIdentifier($this->table) 
			: $this->quoteIdentifier($this->table);
	}
			
}
