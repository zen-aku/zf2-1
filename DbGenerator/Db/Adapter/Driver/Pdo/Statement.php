<?php

namespace Zend\Db\Adapter\Driver\Pdo;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Adapter\Exception;
use Zend\Db\Adapter\ParameterContainer;

class Statement implements StatementInterface {

    /**
     * @var \PDO
     */
    protected $pdo = null;

    /**
     * @var Pdo
     */
    protected $driver = null;

    /**
     * @var string
     */
    protected $sql = '';

    /**
     * @var bool
     */
    protected $isQuery = null;

    /**
     * @var ParameterContainer
     */
    protected $parameterContainer = null;

    /**
     * @var bool
     */
    protected $parametersBound = false;

    /**
     * @var \PDOStatement
     */
    protected $resource = null;

    /**
     *
     * @var bool
     */
    protected $isPrepared = false;

    /**
     * Set driver
     * @param  Pdo $driver
     * @return Statement
     */
    public function setDriver(Adapter $driver) {
        $this->driver = $driver;
        return $this;
    }
	
    /**
     * Initialize
     * @param  \PDO $connectionResource
     * @return Statement
     */
    public function initialize(\PDO $connectionResource) {
        $this->pdo = $connectionResource;
        return $this;
    }

    /**
     * Set resource
     * @param  \PDOStatement $pdoStatement
     * @return Statement
     */
    public function setResource(\PDOStatement $pdoStatement){
        $this->resource = $pdoStatement;
        return $this;
    }

    /**
     * Get resource
     * @return mixed
     */
    public function getResource(){
        return $this->resource;
    }

    /**
     * Set sql
     * @param string $sql
     * @return Statement
     */
    public function setSql($sql){
        $this->sql = $sql;
        return $this;
    }

    /**
     * Get sql
     * @return string
     */
    public function getSql(){
        return $this->sql;
    }

    /**
     * @param ParameterContainer $parameterContainer
     * @return Statement
     */
    public function setParameterContainer(ParameterContainer $parameterContainer){
        $this->parameterContainer = $parameterContainer;
        return $this;
    }

    /**
     * @return ParameterContainer
     */
    public function getParameterContainer(){
        return $this->parameterContainer;
    }

    /**
     * @param string $sql
     * @throws Exception\RuntimeException
     */
    public function prepare($sql = null){
        if ($this->isPrepared) {
            throw new Exception\RuntimeException('This statement has been prepared already');
        }
        if ($sql == null) {
            $sql = $this->sql;
        }
        $this->resource = $this->pdo->prepare($sql);
        
        if ($this->resource === false) {
            $error = $this->pdo->errorInfo();
            throw new Exception\RuntimeException($error[2]);
        }
        $this->isPrepared = true;
    }
    
    /**
     * @return bool
     */
    public function isPrepared(){
        return $this->isPrepared;
    }

    /**
     * @param mixed $parameters
     * @throws Exception\InvalidQueryException
     * @return Result
     */
    public function execute($parameters = null){
        if (!$this->isPrepared) {
            $this->prepare();
        }        
        /** START Standard ParameterContainer Merging Block */
        if (!$this->parameterContainer instanceof ParameterContainer) {
            if ($parameters instanceof ParameterContainer) {
                $this->parameterContainer = $parameters;
                $parameters = null;
            } else {
                $this->parameterContainer = new ParameterContainer();
            }
        }
        if (is_array($parameters)) {
            $this->parameterContainer->setFromArray($parameters);
        }
        if ($this->parameterContainer->count() > 0) {
            $this->bindParametersFromContainer();
        }
        /** END Standard ParameterContainer Merging Block */

        try {
            $this->resource->execute();
        } catch (\PDOException $e) {           
            throw new Exception\InvalidQueryException(
                'Statement could not be executed (' . implode(' - ', $this->resource->errorInfo()) . ')',
                null,
                $e
            );
        }
        $result = $this->driver->createResult($this->resource, $this);
        return $result;
    }

    /**
     * Bind parameters from container
     */
    protected function bindParametersFromContainer(){
        if ($this->parametersBound) {
            return;
        }
        $parameters = $this->parameterContainer->getNamedArray();
        foreach ($parameters as $name => &$value) {
            if (is_bool($value)) {
                $type = \PDO::PARAM_BOOL;
            } elseif (is_int($value)) {
                $type = \PDO::PARAM_INT;
            } else {
                $type = \PDO::PARAM_STR;
            }
            if ($this->parameterContainer->offsetHasErrata($name)) {
                switch ($this->parameterContainer->offsetGetErrata($name)) {
                    case ParameterContainer::TYPE_INTEGER:
                        $type = \PDO::PARAM_INT;
                        break;
                    case ParameterContainer::TYPE_NULL:
                        $type = \PDO::PARAM_NULL;
                        break;
                    case ParameterContainer::TYPE_LOB:
                        $type = \PDO::PARAM_LOB;
                        break;
                }
            }
            // parameter is named or positional, value is reference
            $parameter = is_int($name) ? ($name + 1) : $name;
            $this->resource->bindParam($parameter, $value, $type);
        }
    }

    /**
     * Perform a deep clone
     * @return Statement A cloned statement
     */
    public function __clone(){
        $this->isPrepared = false;
        $this->parametersBound = false;
        $this->resource = null;
        if ($this->parameterContainer) {
            $this->parameterContainer = clone $this->parameterContainer;
        }
    }
    
}