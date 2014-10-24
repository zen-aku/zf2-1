<?php

namespace Zend\Db\Query\Sql;

use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Query\PreparableQueryInterface;
use Zend\Db\Query\AbstractQuery;

/**
 * 
 */
class Sql extends AbstractQuery {
	
	/**
	 * @param string|array|TableIdentifier $table
	 * @return Select
	 */
    public function select($table = null) {
        $this->errorSetTable($table);
        return new Select(($table) ?: $this->table);
    }

	/**
	 * @param string|array|TableIdentifier $table
	 * @return Insert
	 */
    public function insert($table = null) {
        $this->errorSetTable($table);
        return new Insert(($table) ?: $this->table);
    }

	/**
	 * @param string|array|TableIdentifier $table
	 * @return Update
	 */
    public function update($table = null) {
        $this->errorSetTable($table);
        return new Update(($table) ?: $this->table);
    }

	/** 
	 * @param string|array|TableIdentifier $table
	 * @return Delete
	 */
    public function delete($table = null) {
        $this->errorSetTable($table);
        return new Delete(($table) ?: $this->table);
    }

    /**
     * @param PreparableQueryInterface $sqlObject
     * @param StatementInterface|null $statement
     * @return StatementInterface
     */
    public function prepareStatementForSqlObject(PreparableQueryInterface $sqlObject, StatementInterface $statement = null) {
        $statement = ($statement) ?: $this->adapter->getDriver()->createStatement();

        if ($this->sqlPlatform) {
            $this->sqlPlatform->setSubject($sqlObject);
            $this->sqlPlatform->prepareStatement($this->adapter, $statement);
        } else {
            $sqlObject->prepareStatement($this->adapter, $statement);
        }
        return $statement;
    }

}
