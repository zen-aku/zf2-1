<?php

namespace Zend\Db\Adapter;

use Zend\Db\ResultSet;

/**
 * @property Driver\DriverInterface $driver
 */
class Adapter implements AdapterInterface {
    
    /**
     * Query Mode Constants
     */
    const QUERY_MODE_EXECUTE = 'execute';
    const QUERY_MODE_PREPARE = 'prepare';
 
    /**
     * @var Driver\DriverInterface
     */
    protected $driver = null;

    /**
     * @var ResultSet\ResultSetInterface
     */
    protected $queryResultSetPrototype = null;

    /**
     * @var Driver\StatementInterface
     */
    protected $lastPreparedStatement = null;

    /**
     * @param Driver\DriverInterface|array $config
     * @param ResultSet\ResultSetInterface $queryResultPrototype
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($config, ResultSet\ResultSetInterface $queryResultPrototype = null) {
        
        if (!extension_loaded('PDO')) {
            throw new Exception\RuntimeException('The PDO extension is required for this adapter but the extension is not loaded');
        }  
        if (is_array($config)) {     
            $driver = new Driver\Pdo\Pdo($config);          
        } elseif (!$config instanceof Driver\DriverInterface) {
            throw new Exception\InvalidArgumentException(
                'The supplied or instantiated driver object does not implement Zend\Db\Adapter\Driver\DriverInterface'
            );
        }  
        $this->driver = $driver;     		
		$this->queryResultSetPrototype = ($queryResultPrototype) ?: new ResultSet\ResultSet();
    }

    /**
     * getDriver()
     * @throws Exception\RuntimeException
     * @return Driver\DriverInterface
     */
    public function getDriver() {
        if ($this->driver == null) {
            throw new Exception\RuntimeException('Driver has not been set or configured for this adapter.');
        }
        return $this->driver;
    }

    /**
     * @return ResultSet\ResultSetInterface
     */
    public function getQueryResultSetPrototype(){
        return $this->queryResultSetPrototype;
    }

    /**
     * @return 
     */
    public function getCurrentSchema(){
        return $this->driver->getCurrentSchema();
    }

    /**
     * query() is a convenience function
     * @param string $sql
     * @param string|array|ParameterContainer $parametersOrQueryMode
     * @throws Exception\InvalidArgumentException
     * @return Driver\StatementInterface|ResultSet\ResultSet
     */
    public function query( $sql, $parametersOrQueryMode = self::QUERY_MODE_PREPARE, ResultSet\ResultSetInterface $resultPrototype = null ) {
        if (is_string($parametersOrQueryMode) && in_array($parametersOrQueryMode, array(self::QUERY_MODE_PREPARE, self::QUERY_MODE_EXECUTE))) {
            $mode = $parametersOrQueryMode;
            $parameters = null;
        } elseif (is_array($parametersOrQueryMode) || $parametersOrQueryMode instanceof ParameterContainer) {
            $mode = self::QUERY_MODE_PREPARE;
            $parameters = $parametersOrQueryMode;
        } else {
            throw new Exception\InvalidArgumentException('Parameter 2 to this method must be a flag, an array, or ParameterContainer');
        }

        if ($mode == self::QUERY_MODE_PREPARE) {
            $this->lastPreparedStatement = null;
            $this->lastPreparedStatement = $this->driver->createStatement($sql);
            $this->lastPreparedStatement->prepare();
            if (is_array($parameters) || $parameters instanceof ParameterContainer) {
                $this->lastPreparedStatement->setParameterContainer((is_array($parameters)) ? new ParameterContainer($parameters) : $parameters);
                $result = $this->lastPreparedStatement->execute();
            } else {
                return $this->lastPreparedStatement;
            }
        } else {
            $result = $this->driver->execute($sql);
        }

        if ($result instanceof Driver\ResultInterface && $result->isQueryResult()) {
            $resultSet = clone ($resultPrototype ?: $this->queryResultSetPrototype);
            $resultSet->initialize($result);
            return $resultSet;
        }
        return $result;
    }

    /**
     * Create statement
     * @param  string $initialSql
     * @param  ParameterContainer $initialParameters
     * @return Driver\StatementInterface
     */
    public function createStatement($initialSql = null, $initialParameters = null) {
        $statement = $this->driver->createStatement($initialSql);
        if ($initialParameters == null || !$initialParameters instanceof ParameterContainer && is_array($initialParameters)) {
            $initialParameters = new ParameterContainer((is_array($initialParameters) ? $initialParameters : array()));
        }
        $statement->setParameterContainer($initialParameters);
        return $statement;
    }
}
