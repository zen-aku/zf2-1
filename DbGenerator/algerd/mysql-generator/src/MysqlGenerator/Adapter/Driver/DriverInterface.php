<?php

namespace MysqlGenerator\Adapter\Driver;

interface DriverInterface {
 
    /**
     * @const PARAMETERIZATION_NAMED
     */
    const PARAMETERIZATION_NAMED = 'named';
    
    /**
     * Get resource
     * @return mixed
     */
    public function getResource();

    /**
     * Connect
     * @return ConnectionInterface
     */
    public function connect();

    /**
     * Is connected
     * @return bool
     */
    public function isConnected();

    /**
     * Disconnect
     * @return ConnectionInterface
     */
    public function disconnect();

    /**
     * Begin transaction
     * @return ConnectionInterface
     */
    public function beginTransaction();

    /**
     * Commit
     * @return ConnectionInterface
     */
    public function commit();

    /**
     * Rollback
     * @return ConnectionInterface
     */
    public function rollback();

    /**
     * Execute
     * @param  string $sql
     * @return ResultInterface
     */
    public function execute($sql);

    /**
     * Get last generated id
     * @param  null $name Ignored
     * @return int
     */
    public function getLastGeneratedValue($name = null);

    /**
     * Create statement
     * @param string|resource $sqlOrResource
     * @return StatementInterface
     */
    public function createStatement($sqlOrResource = null);

    /**
     * Create result
     * @param resource $resource
     * @return ResultInterface
     */
    public function createResult($resource);
  
    /**
     * Format parameter name
     * @param string $name
     * @param mixed  $type
     * @return string
     */
    public function formatParameterName($name, $type = null);
   
    /**
     * Quote identifier
     * @param  string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier);

    /**
     * Quote identifier chain
     * @param string|string[] $identifierChain
     * @return string
     */
    public function quoteIdentifierChain($identifierChain);

    /**
     * Quote value
     * Will throw a notice when used in a workflow that can be considered "unsafe"
     * @param  string $value
     * @return string
     */
    public function quoteValue($value);

    /**
     * Quote identifier in fragment
     * @param  string $identifier
     * @param  array $additionalSafeWords
     * @return string
     */
    public function quoteIdentifierInFragment($identifier, array $additionalSafeWords = array());
    
}
