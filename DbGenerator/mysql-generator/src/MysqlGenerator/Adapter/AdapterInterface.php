<?php

namespace MysqlGenerator\Adapter;

use MysqlGenerator\Sql\PreparableSqlInterface;
use MysqlGenerator\Sql\SqlInterface;
use MysqlGenerator\Adapter\Driver\StatementInterface;

interface AdapterInterface {
 
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
     * Получить объект Statement с подготовленной стркой запроса и параметрами в его свойстве-объекте ParameterContainer
	 * из PreparableSqlInterface объекта
     * @param PreparableSqlInterface $sqlObject
     * @param StatementInterface|null $statement
     * @return StatementInterface
     */
    public function prepareStatementForSqlObject( PreparableSqlInterface $sqlObject, StatementInterface $statement = null );
    
    /**
	 * Выполнить подготовленный запрос из PreparableSqlInterface объекта
	 * @param PreparableSqlInterface $sqlObject
	 * @param StatementInterface $statement
	 * @return Result
	 */
	public function execPrepareStatement( $sqlObject, StatementInterface $statement = null );
    
    /**
	 * Получить строку запроса из SqlInterface объекта
     * @param \MysqlGenerator\Sql\SqlInterface $sqlObject
     * @param AdapterInterface $adapter
     * @return string
     */
    public function getSqlStringForSqlObject( SqlInterface $sqlObject, AdapterInterface $adapter = null );
    
    /**
	 * Выполнить строку запроса из SqlInterface объекта или 
	 * преобразовать числовой массив SqlInterface объектов в мультистроку запроса и выполнить её
	 * @param SqlInterface array|$sqlObject
	 * @param \MysqlGenerator\Adapter\AdapterInterface $adapter
	 * @return array|Result
	 */
	public function execSqlObject($sqlObject, AdapterInterface $adapter = null);
    
    
    /**
     * Format parameter name
     * @param string $name
     * @param mixed  $type
     * @return string
     */
    public function formatParameterName($name, $type = null);
   
    /**
     * Quote value
     * Will throw a notice when used in a workflow that can be considered "unsafe"
     * @param  string $value
     * @return string
     */
    public function quoteValue($value);

}
