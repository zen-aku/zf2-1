<?php

namespace Zend\Db\Adapter;

use Zend\Db\ResultSet;

/**
 * @property Driver\DriverInterface $driver
 * @property Platform\PlatformInterface $platform
 */
class Adapter implements AdapterInterface
{
    /**
     * Query Mode Constants
     */
    const QUERY_MODE_EXECUTE = 'execute';
    const QUERY_MODE_PREPARE = 'prepare';

    /**
     * Prepare Type Constants
     */
    const PREPARE_TYPE_POSITIONAL = 'positional';
    const PREPARE_TYPE_NAMED = 'named';

    const FUNCTION_FORMAT_PARAMETER_NAME = 'formatParameterName';
    const FUNCTION_QUOTE_IDENTIFIER = 'quoteIdentifier';
    const FUNCTION_QUOTE_VALUE = 'quoteValue';

    const VALUE_QUOTE_SEPARATOR = 'quoteSeparator';

    /**
     * @var Driver\DriverInterface
     */
    protected $driver = null;

    /**
     * @var Platform\PlatformInterface
     */
    protected $platform = null;

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
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($config) {
        if (is_array($config)) {     
            $driver = $this->createDriver($config);
        } elseif (!$config instanceof Driver\DriverInterface) {
            throw new Exception\InvalidArgumentException(
                'The supplied or instantiated driver object does not implement Zend\Db\Adapter\Driver\DriverInterface'
            );
        }
		// проверка драйвера на сервевре
        $driver->checkEnvironment();
        $this->driver = $driver;     		
		$this->platform = new Platform\Mysql($this->driver);
		$this->queryResultSetPrototype = new ResultSet\ResultSet();
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
     * @return Platform\PlatformInterface
     */
    public function getPlatform(){
        return $this->platform;
    }

    /**
     * @return ResultSet\ResultSetInterface
     */
    public function getQueryResultSetPrototype(){
        return $this->queryResultSetPrototype;
    }

    public function getCurrentSchema(){
        return $this->driver->getConnection()->getCurrentSchema();
    }

    /**
     * query() is a convenience function
     * @param string $sql
     * @param string|array|ParameterContainer $parametersOrQueryMode
     * @throws Exception\InvalidArgumentException
     * @return Driver\StatementInterface|ResultSet\ResultSet
     */
    public function query($sql, $parametersOrQueryMode = self::QUERY_MODE_PREPARE, ResultSet\ResultSetInterface $resultPrototype = null)
    {
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
            $result = $this->driver->getConnection()->execute($sql);
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

    /**
     * @param array $parameters
     * @return Driver\DriverInterface
     * @throws \InvalidArgumentException
     * @throws Exception\InvalidArgumentException
     */
    protected function createDriver($parameters) {
        if (!isset($parameters['driver'])) {
            throw new Exception\InvalidArgumentException(__FUNCTION__ . ' expects a "driver" key to be present inside the parameters');
        }
        if ($parameters['driver'] instanceof Driver\DriverInterface) {
            return $parameters['driver'];
        }
        if (!is_string($parameters['driver'])) {
            throw new Exception\InvalidArgumentException(__FUNCTION__ . ' expects a "driver" to be a string or instance of DriverInterface');
        }
        $options = array();
        if (isset($parameters['options'])) {
            $options = (array) $parameters['options'];
            unset($parameters['options']);
        }
        $driverName = strtolower($parameters['driver']);
		
		if ($driverName == 'pdo' || strpos($driverName, 'pdo') === 0) {
            $driver = new Driver\Pdo\Pdo($parameters);
        }		
        if (!isset($driver) || !$driver instanceof Driver\DriverInterface) {
            throw new Exception\InvalidArgumentException('DriverInterface expected', null, null);
        }
        return $driver;
    }

}
