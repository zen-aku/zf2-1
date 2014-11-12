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
		//'join'		=> null,
		
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
		$from = $this->keywords['from'] = new Keyword\From($table, $schema);
		if ($columns = $this->keywords['columns'] instanceof Keyword\SelectColumns
			&&  $columns->hasPrefixColumns()) 
		{		
			$columns->setQuotePrefix($from->getQuotePrefix());
		}
		return $this;
	}
	
	/**
     * @param  array $columns
     *   array(*)
     *   array(value, ...)
     *     value can be strings or Expression objects
     *   array(string => value, ...)
     *     key string will be use as alias,
     *     value can be string or Expression objects
     * @param  bool $hasPrefixColumns
     * @return Select
     */
    public function columns(array $columns, $hasPrefixColumns = true) {
        $col = $this->keywords['columns'] = new Keyword\SelectColumns($columns, $hasPrefixColumns);		
		if ($this->keywords['from']  instanceof Keyword\From 
			&& $hasPrefixColumns) 
		{
			$col->setQuotePrefix($this->keywords['from']->getQuotePrefix());
		}
		return $this;
    }
	
		
}
