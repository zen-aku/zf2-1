<?php

namespace MysqlGenerator\Sql;

use MysqlGenerator\Sql\Keyword;

class Insert extends AbstractDml {
	
	const INSERT = 'INSERT';
	const IGNORE = 'IGNORE';
	const LOW_PRIORITY = 'LOW_PRIORITY';
	const HIGH_PRIORITY = 'HIGH_PRIORITY';
	
	/**
	 * @var array
	 */
	protected $keywords = array(
		'insert'		=> self::INSERT,
		'priority'		=> null,
		'ignore'		=> null,
		'into'			=> null,
		'partition'		=> null,
		'columns'		=> null,
		'values'		=> null,
		'select'		=> null,
		'onDuplicateKey'=> null,	
	);
	
	/**
     * @param string $table
	 * @param string $schema
     */
    public function __construct($table = null, $schema = null, array $options = array()) {
		if ($table) $this->into($table, $schema);
		$this->setOptions($options);
    }
	
	/**
	 * Задать массив опциональных ключей: self::IGNORE, self::LOW_PRIORITY, self::HIGH_PRIORITY
	 * @param array $options
	 */
	public function setOptions(array $options) {
		foreach ( $options as $value ) {
			switch (strtolower($value)) {
				case strtolower(self::IGNORE) : 
					$this->keywords['ignore'] = self::IGNORE;
					break;
				case strtolower(self::LOW_PRIORITY) :
					$this->keywords['priority'] = self::LOW_PRIORITY;
					break;
				case strtolower(self::HIGH_PRIORITY) :
					$this->keywords['priority'] = self::HIGH_PRIORITY;
					break;
			} 
		}
	}
	
	/**
     * @param  string|TableIdentifier $table
     * @return Insert
     */
    public function into($table, $schema = null) {
		$this->keywords['into'] = new Keyword\Into($table, $schema);
        return $this;
    }
	
	/**
	 * @param array $columns
	 * @return Insert
	 */
	public function columns(array $columns) {
		$this->keywords['values'] = null;
		$this->keywords['select'] = null;
		$this->keywords['columns'] = new Keyword\InsertColumns($columns);
        return $this;
    }
	
	/**
	 * @param array $parts
	 * @return Insert
	 */
	public function partitions(array $parts) {
		$this->keywords['partition'] = new Keyword\Partition($parts);
        return $this;
	}
	
	/**
	 * @param string $part
	 * @return Insert
	 */
	public function addPartition($part) {	
		if (!$this->keywords['partition']) $this->keywords['partition'] = new Keyword\Partition($part);
        $this->keywords['partition']->addColumn($part);
		return $this;
	}
	
	/**
	 * @param string $columns
	 * @return Insert
	 */
	public function addColumn($column) {
		$this->keywords['values'] = null;
		$this->keywords['select'] = null;
		if (!$this->keywords['columns']) $this->keywords['columns'] = new Keyword\InsertColumns();
		$this->keywords['columns']->addColumn($column);
		return $this;
	}
	
	/**
	 * @return Insert
	 */
	public function ignore() {
		$this->keywords['ignore'] = self::IGNORE;
		return $this;
	}
	
	/**
	 * @return Insert
	 */
	public function lowPriority() {
		$this->keywords['priority'] = self::LOW_PRIORITY;
		return $this;
	}
	/**
	 * @return Insert
	 */
	public function highPriority() {
		$this->keywords['priority'] = self::HIGH_PRIORITY;
		return $this;
	}
	
	public function select(Select $select) {
		$this->keywords['values'] = null;
		$this->keywords['select'] = $select;
		return $this;
	}
	
	
	/**
	 * "INSERT INTO tbl_name VALUES(null,2,3),(null,5,6),(null,8,9),..."
	 * "INSERT INTO tbl_name(a,b,c) VALUES(1,2,3),(4,5,6),(7,8,9),..."
	 * "INSERT INTO tbl_name (a,b) VALUES((SELECT...), (SELECT...)), ((SELECT...), (SELECT...)), ..."
	 * @param array $values
	 */
	public function values( array $values ) {
		if (!$this->keywords['values']) $this->keywords['values'] = new Keyword\Value();
		
		switch ($this->keywords['values']->checkValues($values)) {
			case Keyword\Value::ARRAY_ARRAY : 
				$this->keywords['values']->addValues($values);
				break;	
			
			case Keyword\Value::ASSOC_ARRAY : 
				$this->keywords['values'] = new Keyword\Value(); 				
				foreach ($values as $key => $value) {				
					$columns[] = $key;
					$data[] = $value;          
				}
				$this->keywords['columns'] = new Keyword\InsertColumns($columns);
				$values = $data;
				
			case Keyword\Value::ROW_ARRAY : 
				$this->keywords['values']->addRowValues($values);			
				break;	
				
			default : 
				throw new Exception\InvalidArgumentException(
					'В MysqlGenerator\Sql\Insert::values() неправильно задан аргумент.'
				);			
		}
		$this->keywords['select'] = null;
		return $this;
	}
	
}
