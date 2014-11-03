<?php

namespace MysqlGenerator\Adapter\AdapterTrait;

/** 
 * 
 */
trait TransactionTrait {
    
    /**
     * @var bool
     */
    protected $inTransaction = false;
    
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
   
}

