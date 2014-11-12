<?php

namespace MysqlGenerator\Sql;

use MysqlGenerator\Sql\Keyword;

class Select extends AbstractDml {
	
	
	const SELECT = 'SELECT';
	
	/**
	 * @var array
	 */
	protected $keywords = array(
		'select'	=> self::SELECT,
		'columns'	=> null,
		'from'		=> null,
		'join'		=> null,
		
	);	
		
	/**
     * @param string|array $table
	 * @param string $schema
     */
    public function __construct($table = null, $schema = null){
		if ($table) $this->from($table, $schema);
    }

	/** 
	 * @param string|array $table
	 * @param string $schema
	 * @return Select
	 */
	public function from($table, $schema = null){
		$this->keywords['from'] = new Keyword\From($table, $schema);
		if ($this->keywords['columns'] instanceof Keyword\SelectColumns) {
			$this->keywords['columns']->setTable($this->keywords['from']);
		}
		return $this;
	}
	
	/**
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
     * @return Select
     */
    public function columns(array $columns, $prefixColumnsWithTable = true){
        $this->keywords['columns'] = new Keyword\SelectColumns($columns, $prefixColumnsWithTable);
        if ($this->keywords['from']  instanceof Keyword\From) {
			$this->keywords['columns']->setTable($this->keywords['from']);
		}
		return $this;
    }
	
	
	
}
