<?php

namespace MysqlGenerator\Adapter;

use MysqlGenerator\ResultSet;
use MysqlGenerator\Adapter\Driver\Statement;
use MysqlGenerator\Adapter\Driver\Result;
use MysqlGenerator\Adapter\Driver\Feature\AbstractFeature;
use MysqlGenerator\Sql\SqlInterface;
use MysqlGenerator\Sql\PreparableSqlInterface;
use MysqlGenerator\Adapter\Driver\StatementInterface;

class Adapter implements AdapterInterface {
    
    use AdapterTrait\TransactionTrait;
    use AdapterTrait\QuoteTrait;
    
    /**
     * Query Mode Constants
     */
    const QUERY_MODE_EXECUTE = 'execute';
    const QUERY_MODE_PREPARE = 'prepare';
 
    /**
     * @var string
     */
    protected $dsn;
    
    /**
     * @var string
     */
    private $username;
    
    /**
     * @var string
     */
    private $password;
    
    /**
     * @var array
     */
    protected $options;

    /**
     * @var \PDO
     */
    protected $resource = null;
       
    /**
     * @var array
     */
    protected $features = array();   
         
    /**
     * @var Statement
     */
    protected $statementPrototype = null;

    /**
     * @var Result
     */
    protected $resultPrototype = null;
    
     /**
     * @var ResultSet\ResultSetInterface
     */
    protected $queryResultSetPrototype = null;

    /**
     * @var Driver\StatementInterface
     */
    protected $lastPreparedStatement = null;
    
    /**
     * @param array|\PDO $connection
     * @param ResultSet\ResultSetInterface $queryResultPrototype
	 * @param Result $resultPrototype $features
	 * @param array|AbstractFeature 
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(
		$connection, 
		Result $resultPrototype = null, 
		ResultSet\ResultSetInterface $queryResultPrototype = null,
		$features = null) 
	{        
        if (!extension_loaded('PDO')) {
            throw new Exception\RuntimeException('The PDO extension is required for this adapter but the extension is not loaded');
        }        
        // connection
        if (is_array($connection)) {
            $this->setConnectionParameters($connection);
        } elseif ($connection instanceof \PDO) {
            $this->setResource($connection);
        } elseif (null !== $connection) {
            throw new Exception\InvalidArgumentException('$connection must be an array of parameters, a PDO object or null');
        }
        // features         
        if (is_array($features)) {
            foreach ($features as $name => $feature) {
                $this->addFeature($name, $feature);
            }
        } elseif ($features instanceof AbstractFeature) {
            $this->addFeature($features->getName(), $features);
        } 
		// initialize		
		$this->registerStatementPrototype(($statementPrototype) ?: new Statement());
        $this->registerResultPrototype(($resultPrototype) ?: new Result());
		$this->queryResultSetPrototype = ($queryResultPrototype) ?: new ResultSet\ResultSet();
    }
    
    /**
     * Connect
     * @return this
     * @throws Exception\RuntimeException
     */
    public function connect() {
        if ($this->resource) {
            return $this;
        }
        try {
            $this->resource = new \PDO($this->dsn, $this->username, $this->password, $this->options);
            $this->resource->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } 
		catch (\PDOException $e) {
            $code = $e->getCode();
            if (!is_long($code)) {
                $code = null;
            }
            throw new Exception\RuntimeException('Connect Error: ' . $e->getMessage(), $code, $e);
        }
        return $this;
    }
    
    /**
     * Set resource
     * @param  \PDO $resource
     * @return Connection
     */
    public function setResource(\PDO $resource) {
        $this->resource = $resource;
        return $this;
    }

    /**
     * Get resource 
     * @return \PDO
     */
    public function getResource() {
        if (!$this->isConnected()) {
            $this->connect();
        }
        return $this->resource;
    }
    
    /**
     * Set connection parameters: dsn, username, password, options
     * @param array $connectionParameters
     * @throw Exception\InvalidConnectionParametersException
     * @return this;
     */
    public function setConnectionParameters(array $connectionParameters) {        
        $dsn = $username = $password = $hostname = $database = null;
        $options = array();
        foreach ($connectionParameters as $key => $value) {
            switch (strtolower($key)) {
                case 'dsn':
                    $dsn = $value;
                    break;
                case 'user':
                case 'username':
                    $username = (string) $value;
                    break;
                case 'pass':
                case 'password':
                    $password = (string) $value;
                    break;
                case 'host':
                case 'hostname':
                    $hostname = (string) $value;
                    break;
                case 'port':
                    $port = (int) $value;
                    break;
                case 'database':
                case 'dbname':
                    $database = (string) $value;
                    break;
                case 'charset':
                    $charset    = (string) $value;
                    break;
                case 'options':
                    $value = (array) $value;
                    $options = array_diff_key($options, $value) + $value;
                    break;
                default:
                    $options[$key] = $value;
                    break;
            }
        }
        if (!isset($dsn)) {			
            $dsn = array();
			if (isset($database)) {
				$dsn[] = "dbname={$database}";
			}
			if (isset($hostname)) {
				$dsn[] = "host={$hostname}";
			}
			if (isset($port)) {
				$dsn[] = "port={$port}";
			}
			if (isset($charset)) {
				$dsn[] = "charset={$charset}";
			}                
            $dsn = 'mysql' . ':' . implode(';', $dsn);			
        } 
		elseif (!isset($dsn)) {
            throw new Exception\InvalidConnectionParametersException(
                'A dsn was not provided or could not be constructed from your parameters',
                $connectionParameters
            );
        }
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->options = $options;
        return $this;    
    }
       
    /**
     * @return string
     */
    public function getDsn() {
        return $this->dsn;
    }
    
    /**
     * @return array
     */
    public function getDriverOptions() {
        return $this->options;
    }
    
    /**
     * Is connected
     * @return bool
     */
    public function isConnected() {
        return ($this->resource instanceof \PDO);
    }

    /**
     * Disconnect
     * @return Connection
     */
    public function disconnect() {
        if ($this->isConnected()) {
            $this->resource = null;
        }
        return $this;
    }
    
    /**
     * Execute
     * @param $sql
     * @return Result
     * @throws Exception\InvalidQueryException
     */
    public function execute($sql) {
        if (!$this->isConnected()) {
            $this->connect();
        }
        $resultResource = $this->resource->query($sql);

        if ($resultResource === false) {
            $errorInfo = $this->resource->errorInfo();
            throw new Exception\InvalidQueryException($errorInfo[2]);
        }
        $result = $this->createResult($resultResource, $sql);
        return $result;
    }

    /**
     * Prepare
     * @param string $sql
     * @return Statement
     */
    public function prepare($sql){
        if (!$this->isConnected()) {
            $this->connect();
        }
        $statement = $this->createStatement($sql);
        return $statement;
    }

    /**
     * Get last generated id
     * @param string $name
     * @return string|null|false
     */
    public function getLastGeneratedValue($name = null) {
        try {
            return $this->resource->lastInsertId($name);
        } 
		catch (\Exception $e) {
            // do nothing
        }
        return false;
    }  
    
    /**
     * Get current schema
     * @return string
     */
    public function getCurrentSchema() {
        if (!$this->isConnected()) {
            $this->connect();
        }           
        $result = $this->resource->query('SELECT DATABASE()');
        if ($result instanceof \PDOStatement) {
            return $result->fetchColumn();
        }
        return false;
    }
    
    /**
     * Register statement prototype
     * @param Statement $statementPrototype
     */
    public function registerStatementPrototype(Statement $statementPrototype) {
        $this->statementPrototype = $statementPrototype;
        $this->statementPrototype->setDriver($this);
    }

    /**
     * Register result prototype
     * @param Result $resultPrototype
     */
    public function registerResultPrototype(Result $resultPrototype) {
        $this->resultPrototype = $resultPrototype;
        return $this;
    }
    
    /**
     * @return ResultSet\ResultSetInterface
     */
    public function getQueryResultSetPrototype(){
        return $this->queryResultSetPrototype;
    }

    /**
     * Add feature
     * @param string $name
     * @param AbstractFeature $feature
     * @return 
     */
    public function addFeature($name, $feature) {
        if ($feature instanceof AbstractFeature) {
            $name = $feature->getName(); // overwrite the name, just in case
            $feature->setDriver($this);
        }
        $this->features[$name] = $feature;
        return $this;
    }

    /**
     * Get feature
     * @param $name
     * @return AbstractFeature|false
     */
    public function getFeature($name) {
        if (isset($this->features[$name])) {
            return $this->features[$name];
        }
        return false;
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
            $this->lastPreparedStatement = $this->createStatement($sql);
            $this->lastPreparedStatement->prepare();
            if (is_array($parameters) || $parameters instanceof ParameterContainer) {
                $this->lastPreparedStatement->setParameterContainer((is_array($parameters)) ? new ParameterContainer($parameters) : $parameters);
                $result = $this->lastPreparedStatement->execute();
            } else {
                return $this->lastPreparedStatement;
            }
        } else {
            $result = $this->execute($sql);
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
    public function createStatement( $initialSql = null, $initialParameters = null ) { 
        $statement = clone $this->statementPrototype;
        if ($initialSql instanceof \PDOStatement) {
            $statement->setResource($initialSql);
        } else {
            if (is_string($initialSql)) {
                $statement->setSql($initialSql);
            }
            if (!$this->isConnected()) {
                $this->connect();
            }
            $statement->initialize($this->getResource());
        }
              
        if ($initialParameters == null || !$initialParameters instanceof ParameterContainer && is_array($initialParameters)) {
            $initialParameters = new ParameterContainer((is_array($initialParameters) ? $initialParameters : array()));
        }
        $statement->setParameterContainer($initialParameters);
        return $statement;
    }
    
    /**
     * @param resource $resource
     * @return Result
     */
    public function createResult($resource) {
        $result = clone $this->resultPrototype;  
        $result->initialize($resource, $this->getLastGeneratedValue());
        return $result;
    }

    /**
     * @param string $name
     * @param string|null $type
     * @return string
     */
    public function formatParameterName($name, $type = null) {
        if ($type == null && !is_numeric($name) || $type == self::PARAMETERIZATION_NAMED) {
            return ':' . $name;
        }
        return '?';
    }
    
    /**
     * Получить объект Statement с подготовленной стркой запроса и параметрами в его свойстве-объекте ParameterContainer
	 * из PreparableSqlInterface объекта
     * @param PreparableSqlInterface $sqlObject
     * @param StatementInterface|null $statement
     * @return StatementInterface
     */
    public function prepareStatementForSqlObject( PreparableSqlInterface $sqlObject, StatementInterface $statement = null ) {
        $statement = ($statement) ?: $this->createStatement();
		$sqlObject->prepareStatement($this, $statement);	
        return $statement;
    }
	
	/**
	 * Выполнить подготовленный запрос из PreparableSqlInterface объекта
	 * @param PreparableSqlInterface $sqlObject
	 * @param StatementInterface $statement
	 * @return Result
	 */
	public function execPrepareStatement( $sqlObject, StatementInterface $statement = null ) {
		return $this->prepareStatementForSqlObject($sqlObject, $statement)->execute();
	}

    /**
	 * Получить строку запроса из SqlInterface объекта
     * @param \MysqlGenerator\Sql\SqlInterface $sqlObject
     * @param AdapterInterface $adapter
     * @return string
     */
    public function getSqlStringForSqlObject( SqlInterface $sqlObject, AdapterInterface $adapter = null ){
		$adapter = ($adapter) ?: $this;    
        $sqlString = $sqlObject->getSqlString($adapter);		
        return $sqlString;
    }
	
	/**
	 * Выполнить строку запроса из SqlInterface объекта или 
	 * преобразовать числовой массив SqlInterface объектов в мультистроку запроса и выполнить её
	 * @param SqlInterface array|$sqlObject
	 * @param \MysqlGenerator\Adapter\AdapterInterface $adapter
	 * @return array|Result
	 */
	public function execSqlObject($sqlObject, AdapterInterface $adapter = null) {		
		$sqlString = '';
		
		if ($sqlObject instanceof SqlInterface) {
			$sqlString = $this->getSqlStringForSqlObject( $sqlObject, $adapter);
		}
		elseif (is_array($sqlObject)) {		
			foreach ($sqlObject as $object) {
				if ($object instanceof SqlInterface) {
					if (!$sqlString) {
						$sqlString = $this->getSqlStringForSqlObject( $object, $adapter);
					} else {
						$sqlString .= ';' . $this->getSqlStringForSqlObject( $object, $adapter);
					}	
				}
				else {
					$sqlString = false;
					break;
				}
			}
		}
		if (!$sqlString) {
			throw new Exception\InvalidArgumentException('$sqlObject must be a SqlInterface object or an array of SqlInterface objects and not null');
		}
		return $this->query( $sqlString, self::QUERY_MODE_EXECUTE );
		// если параметр метода $sqlObject только объект SqlInterface
		//return $this->query($this->getSqlStringForSqlObject( $sqlObject, $adapter), self::QUERY_MODE_EXECUTE);
	}
       
}
