<?php

namespace Zend\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Driver\Feature\AbstractFeature;
use Zend\Db\Adapter\Driver\Feature\DriverFeatureInterface;
use Zend\Db\Adapter\Exception;

class Pdo implements DriverInterface, DriverFeatureInterface {
      
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
     * @var Statement
     */
    protected $statementPrototype = null;

    /**
     * @var Result
     */
    protected $resultPrototype = null;

    /**
     * @var array
     */
    protected $features = array();   
        
    /**
     * @var bool
     */
    protected $inTransaction = false;
    
    /**
     * @param array|\PDO $connection
     * @param null|Statement $statementPrototype
     * @param null|Result $resultPrototype
     * @param string $features
     */
    public function __construct($connection, Statement $statementPrototype = null, Result $resultPrototype = null, $features = null ) {
             
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
                case 'driver':
                    $value = strtolower($value);
                    if (strpos($value, 'pdo') === 0) {
                        $pdoDriver = strtolower(substr(str_replace(array('-', '_', ' '), '', $value), 3));
                    }
                    break;
                case 'pdodriver':
                    $pdoDriver = (string) $value;
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
                case 'driver_options':
                case 'options':
                    $value = (array) $value;
                    $options = array_diff_key($options, $value) + $value;
                    break;
                default:
                    $options[$key] = $value;
                    break;
            }
        }
        if (!isset($dsn) && isset($pdoDriver)) {			
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
            $dsn = $pdoDriver . ':' . implode(';', $dsn);			
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
     * @param string|\PDOStatement $sqlOrResource
     * @return Statement
     */
    public function createStatement($sqlOrResource = null) {
        $statement = clone $this->statementPrototype;
        if ($sqlOrResource instanceof \PDOStatement) {
            $statement->setResource($sqlOrResource);
        } else {
            if (is_string($sqlOrResource)) {
                $statement->setSql($sqlOrResource);
            }
            if (!$this->isConnected()) {
                $this->connect();
            }
            $statement->initialize($this->getResource());
        }
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
       
}
