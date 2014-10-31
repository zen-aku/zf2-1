<?php

namespace MysqlGenerator\Sql;

class Sql {
	
    /** 
	 * @var string|array|TableIdentifier 
	 */
    protected $table = null;

	/**
	 * @param type $table
	 */
    public function __construct($table = null) {
        if ($table) {
            $this->setTable($table);
        }
    }

	/**
	 * @return boolean
	 */
    public function hasTable(){
        return ($this->table != null);
    }

	/**
	 * @param \MysqlGenerator\Sql\TableIdentifier $table
	 * @return \MysqlGenerator\Sql\Sql
	 * @throws Exception\InvalidArgumentException
	 */
    public function setTable($table){
        if (is_string($table) || is_array($table) || $table instanceof TableIdentifier) {
            $this->table = $table;
        } else {
            throw new Exception\InvalidArgumentException('Table must be a string, array or instance of TableIdentifier.');
        }
        return $this;
    }

	/**
	 * @return type
	 */
    public function getTable(){
        return $this->table;
    }
	
	/**
	 * @param type $table
	 * @return \MysqlGenerator\Sql\Select
	 * @throws Exception\InvalidArgumentException
	 */
    public function select($table = null){
        if ($this->table !== null && $table !== null) {
            throw new Exception\InvalidArgumentException(sprintf(
                'This Sql object is intended to work with only the table "%s" provided at construction time.',
                $this->table
            ));
        }
        return new Select(($table) ?: $this->table);
    }

	/**
	 * @param type $table
	 * @return \MysqlGenerator\Sql\Insert
	 * @throws Exception\InvalidArgumentException
	 */
    public function insert($table = null){
        if ($this->table !== null && $table !== null) {
            throw new Exception\InvalidArgumentException(sprintf(
                'This Sql object is intended to work with only the table "%s" provided at construction time.',
                $this->table
            ));
        }
        return new Insert(($table) ?: $this->table);
    }

	/**
	 * @param type $table
	 * @return \MysqlGenerator\Sql\Update
	 * @throws Exception\InvalidArgumentException
	 */
    public function update($table = null){
        if ($this->table !== null && $table !== null) {
            throw new Exception\InvalidArgumentException(sprintf(
                'This Sql object is intended to work with only the table "%s" provided at construction time.',
                $this->table
            ));
        }
        return new Update(($table) ?: $this->table);
    }

	/**
	 * @param type $table
	 * @return \MysqlGenerator\Sql\Delete
	 * @throws Exception\InvalidArgumentException
	 */
    public function delete($table = null){
        if ($this->table !== null && $table !== null) {
            throw new Exception\InvalidArgumentException(sprintf(
                'This Sql object is intended to work with only the table "%s" provided at construction time.',
                $this->table
            ));
        }
        return new Delete(($table) ?: $this->table);
    }

}
