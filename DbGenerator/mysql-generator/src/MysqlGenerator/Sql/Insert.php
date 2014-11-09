<?php
namespace MysqlGenerator\Sql;

use MysqlGenerator\Sql\Keyword;

class Insert {
	
	/**
	 * @var array
	 */
	protected $keywords = array (
		'insert'		=> null,
		'priority'		=> null,
		'ignore'		=> null,
		'into'			=> null,
		'partition'		=> null,
		'columns'		=> null,
		'values'		=> null,
		'onDuplicateKey'=> null,	
	);
	
	/**
     * @param string $table
	 * @param string $schema
     */
    public function __construct($table = null, $schema = null) {
		if ($table) $this->into($table, $schema);	
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
		$this->keywords['columns'] = new Keyword\InsertColumns($columns);
        return $this;
    }
	
	/**
	 * @param string $columns
	 * @return Insert
	 */
	public function addColumn($column) {
		if (!$this->keywords['columns']) $this->keywords['columns'] = new Keyword\InsertColumns();
		$this->keywords['columns']->addColumn($column);
		return $this;
	}
	
	/**
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
    public function getSqlString(AdapterInterface $adapter) {
		$sqlString = '';
		foreach ($this->keywords as $keyword) {
			if ($keyword) {
				$sqlString .= $keyword->getString($adapter);
			}
		}
		return $sqlString;	
	}
 
	
}
