<?php

namespace Zend\Db\Sql;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Adapter\Driver\DriverInterface;

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
     * @return null|\Zend\Db\Adapter\AdapterInterface
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
	 * @param \Zend\Db\Sql\TableIdentifier $table
	 * @return \Zend\Db\Sql\Sql
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
	 * @return \Zend\Db\Sql\Select
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
	 * @return \Zend\Db\Sql\Insert
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
	 * @return \Zend\Db\Sql\Update
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
	 * @return \Zend\Db\Sql\Delete
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
        $statement = ($statement) ?: $this->adapter->getDriver()->createStatement();
		$sqlObject->prepareStatement($this->adapter, $statement);
        return $statement;
    }

    /**
     * @param \Zend\Db\Sql\SqlInterface $sqlObject
     * @param DriverInterface $driver
     * @return type
     */
    public function getSqlStringForSqlObject( SqlInterface $sqlObject, DriverInterface $driver = null ){
        $driver = ($driver) ?: $this->adapter->getDriver();    
        $sqlString = $sqlObject->getSqlString($driver);      
        return $sqlString;
    }
	
}