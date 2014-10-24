<?php

namespace Zend\Db\Query\Ddl;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Adapter\Platform\PlatformInterface;

use Zend\Db\Query\TableIdentifier;
use Zend\Db\Query\Platform;
use Zend\Db\Query\Exception;
use Zend\Db\Query\PreparableQueryInterface;

/**
 * 
 */
class Ddl {
    
    /** 
     * @var AdapterInterface 
     */
    protected $adapter = null;

    /** 
     * @var string|array|TableIdentifier 
     */
    protected $table = null;

    /** 
     * @var Platform\Platform 
     */
    protected $sqlPlatform = null;

    /**
     * Инициализация свойств 
     * @param AdapterInterface $adapter
     * @param string|array|\Zend\Db\Query\Ddl\TableIdentifier $table
     * @param \Zend\Db\Query\Ddl\Platform\AbstractPlatform $sqlPlatform
     */
    public function __construct(AdapterInterface $adapter, $table = null, Platform\AbstractPlatform $sqlPlatform = null) {
        $this->adapter = $adapter;
        if ($table) {
            $this->setTable($table);
        }
        $this->sqlPlatform = ($sqlPlatform) ?: new Platform\Platform($adapter);
    }

    /**
     * @return null|\Zend\Db\Adapter\AdapterInterface
     */
    public function getAdapter() {
        return $this->adapter;
    }
    /**
     * @return boolean
     */
    public function hasTable() {
        return ($this->table != null);
    }

    /**
     * @param string|array|\Zend\Db\Query\TableIdentifier $table
     * @return \Zend\Db\Query\Ddl\Ddl
     * @throws Exception\InvalidArgumentException
     */
    public function setTable($table) {
        if (is_string($table) || is_array($table) || $table instanceof TableIdentifier) {
            $this->table = $table;
        } else {
            throw new Exception\InvalidArgumentException('Table must be a string, array or instance of TableIdentifier.');
        }
        return $this;
    }

    /**
     * @return string|array|\Zend\Db\Query\TableIdentifier
     */
    public function getTable() {
        return $this->table;
    }

    /** 
     * @return \Zend\Db\Query\Ddl\Platform\AbstractPlatform
     */
    public function getSqlPlatform() {
        return $this->sqlPlatform;
    }
    
    /**
     * Создать объект-команду класса DropTable
     * @param string $table
     * @return DropTable
     * @throws Exception\InvalidArgumentException 
     */
    public function dropTable( $table = null ) {
        $this->errorSetTable( $table );
        return new DropTable(($table) ?: $this->table);
    }
    
    /**
     * Создать объект-команду класса CreateTable
     * @param string $table
     * @return CreateTable
     * @throws Exception\InvalidArgumentException 
     */
    public function createTable( $table = null ) {
        $this->errorSetTable( $table );
        return new CreateTable(($table) ?: $this->table);
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
    
    /**
     * @param \Zend\Db\Query\Ddl\DdlInterface $sqlObject
     * @param PlatformInterface $platform
     * @return type
     */
    public function getSqlStringForSqlObject(DdlInterface $sqlObject, PlatformInterface $platform = null) {
        $platform = ($platform) ?: $this->adapter->getPlatform();
        if ($this->sqlPlatform) {
            $this->sqlPlatform->setSubject($sqlObject);
            $sqlString = $this->sqlPlatform->getSqlString($platform);
        } else {
            $sqlString = $sqlObject->getSqlString($platform);
        }
        return $sqlString;
    }
    
    /**
     * Нельзя задать $table если уже существует $this->table (ранее задан в конструкторе или через setTable($table))
     * @param string $table
     * @throws Exception\InvalidArgumentException
     */
    private function errorSetTable( $table ) {
        if ($this->table !== null && $table !== null) {
            throw new Exception\InvalidArgumentException(sprintf(
                'This Sql object is intended to work with only the table "%s" provided at construction time.',
                $this->table
            ));
        }
    }
}
