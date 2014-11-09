<?php

namespace MysqlGenerator\Sql\Keyword;

class Columns extends AbstractKeyword {
	
	/**
     * @var array
     */
    protected $columns = array();
	
	/**
	 * @param array $columns
	 */
	public function __construct(array $columns = array()){
		$this->columns = $columns;
    }
	
	/**
	 * @param array $columns
	 */
	public function setColumns(array $columns) {
		$this->columns = $columns;
	}
	
	/**
	 * @param string $column
	 */
	public function addColumn($column) {
		$this->columns[] = $column;
	}
	
	/**
	 * @param string $column
	 */
	public function removeColumn($column) {
		if ( key_exists($column, $this->columns)) unset($this->columns[$column]);
	}
	
	/**
	 * @return int
	 */
	public function count() {
		return count($this->columns);
	}
	
	/**
	 * @return string " `column1`, `column2`, `column3` ... "
	 */
	public function getString() {	
		return implode( ', ', array_map( array($this, 'quoteIdentifier'), $this->columns));
	}
	
}