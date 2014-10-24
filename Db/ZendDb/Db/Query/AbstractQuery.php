<?php

namespace Zend\Db\Query;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Platform\PlatformInterface;

/**
 * 
 */
abstract class AbstractQuery {
	
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
     * @param AdapterInterface $adapter
     * @param string|array|TableIdentifier $table
     * @param Platform\AbstractPlatform $sqlPlatform
     */
    public function __construct(AdapterInterface $adapter, $table = null, Platform\AbstractPlatform $sqlPlatform = null) {
        $this->adapter = $adapter;
        if ($table) {
            $this->setTable($table);
        }
        $this->sqlPlatform = ($sqlPlatform) ?: new Platform\Platform($adapter);
    }

    /**
     * @return null|AdapterInterface
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
     * @param string|array|TableIdentifier $table
     * @return self
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
     * @return string|array|TableIdentifier
     */
    public function getTable() {
        return $this->table;
    }

    /** 
     * @return Platform\AbstractPlatform
     */
    public function getSqlPlatform() {
        return $this->sqlPlatform;
    }
	
	/**
     * @param QueryInterface $sqlObject
     * @param PlatformInterface $platform
     * @return type
     */
    public function getSqlStringForSqlObject(QueryInterface $sqlObject, PlatformInterface $platform = null) {
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
    protected function errorSetTable( $table ) {
        if ($this->table !== null && $table !== null) {
            throw new Exception\InvalidArgumentException(sprintf(
                'This Sql object is intended to work with only the table "%s" provided at construction time.',
                $this->table
            ));
        }
    }
	
}
