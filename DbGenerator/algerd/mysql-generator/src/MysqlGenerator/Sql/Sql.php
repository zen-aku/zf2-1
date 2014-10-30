<?php

namespace MysqlGenerator\Sql;

use MysqlGenerator\Adapter\AdapterInterface;
use MysqlGenerator\Adapter\Driver\StatementInterface;

class Sql {
	
    /** 
	 * @var AdapterInterface 
	 */
    protected $adapter = null;

    /** 
	 * @var string|array|TableIdentifier 
	 */
    protected $table = null;

	/**
	 * @param AdapterInterface $adapter
	 * @param type $table
	 */
    public function __construct(AdapterInterface $adapter, $table = null) {
        $this->adapter = $adapter;
        if ($table) {
            $this->setTable($table);
        }
    }

    /**
     * @return null|\MysqlGenerator\Adapter\AdapterInterface
     */
    public function getAdapter(){
        return $this->adapter;
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

    /**
     * @param PreparableSqlInterface $sqlObject
     * @param StatementInterface|null $statement
     * @return StatementInterface
     */
    public function prepareStatementForSqlObject( PreparableSqlInterface $sqlObject, StatementInterface $statement = null ) {
        $statement = ($statement) ?: $this->adapter->createStatement();
		$sqlObject->prepareStatement($this->adapter, $statement);
        return $statement;
    }

    /**
     * @param \MysqlGenerator\Sql\SqlInterface $sqlObject
     * @param AdapterInterface $adapter
     * @return type
     */
    public function getSqlStringForSqlObject( SqlInterface $sqlObject, AdapterInterface $adapter = null ){
        $adapter = ($adapter) ?: $this->adapter;    
        $sqlString = $sqlObject->getSqlString($adapter);      
        return $sqlString;
    }
	
}
