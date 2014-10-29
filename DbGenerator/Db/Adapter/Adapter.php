<?php

namespace Zend\Db\Adapter;

use Zend\Db\ResultSet;
use Zend\Db\Adapter\Driver\DriverInterface; // Убрать!!!
use Zend\Db\Adapter\Driver\Pdo\Statement;
use Zend\Db\Adapter\Driver\Pdo\Result;

/**
 *
 */
class Adapter implements AdapterInterface, DriverInterface {
    
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
     * @var bool
     */
    protected $inTransaction = false;
    
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
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($connection, ResultSet\ResultSetInterface $queryResultPrototype = null) {
        
        if (!extension_loaded('PDO')) {
            throw new Exception\RuntimeException('The PDO extension is required for this adapter but the extension is not loaded');
        }        
        
        if (is_array($connection)) {
            $this->setConnectionParameters($connection);
        } elseif ($connection instanceof \PDO) {
            $this->setResource($connection);
        } elseif (null !== $connection) {
            throw new Exception\InvalidArgumentException('$connection must be an array of parameters, a PDO object or null');
        }
            
        $this->registerStatementPrototype(($statementPrototype) ?: new Statement());
        $this->registerResultPrototype(($resultPrototype) ?: new Result());
        if (is_array($features)) {
            foreach ($features as $name => $feature) {
                $this->addFeature($name, $feature);
            }
        } elseif ($features instanceof AbstractFeature) {
            $this->addFeature($features->getName(), $features);
        } 
                		
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
        } catch (\PDOException $e) {
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
     * Begin transaction
     * @return Connection
     */
    public function beginTransaction() {
        if (!$this->isConnected()) {
            $this->connect();
        }
        $this->resource->beginTransaction();
        $this->inTransaction = true;
        return $this;
    }

    /**
     * In transaction
     * @return bool
     */
    public function inTransaction() {
        return $this->inTransaction;
    }

    /**
     * Commit
     * @return Connection
     */
    public function commit() {
        if (!$this->isConnected()) {
            $this->connect();
        }
        $this->resource->commit();
        $this->inTransaction = false;
        return $this;
    }

    /**
     * Rollback
     * @return Connection
     * @throws Exception\RuntimeException
     */
    public function rollback() {
        if (!$this->isConnected()) {
            throw new Exception\RuntimeException('Must be connected before you can rollback');
        }
        if (!$this->inTransaction) {
            throw new Exception\RuntimeException('Must call beginTransaction() before you can rollback');
        }
        $this->resource->rollBack();
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
     * Add feature
     * @param string $name
     * @param AbstractFeature $feature
     * @return Pdo
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
     * Create statement
     * @param  string $initialSql
     * @param  ParameterContainer $initialParameters
     * @return Driver\StatementInterface
     */
    public function createStatement( $initialSql = null, $initialParameters = null ) { 
        $statement = clone $this->statementPrototype;
        if ($$initialSql instanceof \PDOStatement) {
            $statement->setResource($$initialSql);
        } else {
            if (is_string($$initialSql)) {
                $statement->setSql($$initialSql);
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
     * @param mixed $context
     * @return Result
     */
    public function createResult($resource, $context = null) {
        $result = clone $this->resultPrototype;
        $rowCount = null;    
        $result->initialize($resource, $this->getLastGeneratedValue(), $rowCount);
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
     * Quote identifier
     * @param  string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier) {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    /**
     * Quote identifier chain
     * @param string|string[] $identifierChain
     * @return string
     */
    public function quoteIdentifierChain($identifierChain) {
        $identifierChain = str_replace('`', '``', $identifierChain);
        if (is_array($identifierChain)) {
            $identifierChain = implode('`.`', $identifierChain);
        }
        return '`' . $identifierChain . '`';
    }
   
    /**
     * Quote value
     * @param  string $value
     * @return string
     */
    public function quoteValue($value) {        
        return $this->quote($value);      
    }
   
    /**
     * Quote identifier in fragment
     * @param  string $identifier
     * @param  array $safeWords
     * @return string
     */
    public function quoteIdentifierInFragment($identifier, array $safeWords = array()) {
        // regex taken from @link http://dev.mysql.com/doc/refman/5.0/en/identifiers.html
        $parts = preg_split('#([^0-9,a-z,A-Z$_])#', $identifier, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if ($safeWords) {
            $safeWords = array_flip($safeWords);
            $safeWords = array_change_key_case($safeWords, CASE_LOWER);
        }
        foreach ($parts as $i => $part) {
            if ($safeWords && isset($safeWords[strtolower($part)])) {
                continue;
            }
            switch ($part) {
                case ' ':
                case '.':
                case '*':
                case 'AS':
                case 'As':
                case 'aS':
                case 'as':
                    break;
                default:
                    $parts[$i] = '`' . str_replace('`', '``', $part) . '`';
            }
        }
        return implode('', $parts);
    }
    
    
///////////////////////////////
    /**
     * Убрать !!!!
     */
    public function getDriver() {
        return $this;
    }

    /**
     * @return ResultSet\ResultSetInterface
     */
    public function getQueryResultSetPrototype(){
        return $this->queryResultSetPrototype;
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

    
}
