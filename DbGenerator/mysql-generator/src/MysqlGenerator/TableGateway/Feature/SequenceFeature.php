<?php

namespace MysqlGenerator\TableGateway\Feature;

use MysqlGenerator\Sql\Insert;
use MysqlGenerator\Adapter\Driver\ResultInterface;
use MysqlGenerator\Adapter\Driver\StatementInterface;

class SequenceFeature extends AbstractFeature {
    
    /**
     * @var string
     */
    protected $primaryKeyField;

    /**
     * @var string
     */
    protected $sequenceName;

    /**
     * @var int
     */
    protected $sequenceValue;


    /**
     * @param string $primaryKeyField
     * @param string $sequenceName
     */
    public function __construct($primaryKeyField, $sequenceName)
    {
        $this->primaryKeyField = $primaryKeyField;
        $this->sequenceName    = $sequenceName;
    }

    /**
     * @param Insert $insert
     * @return Insert
     */
    public function preInsert(Insert $insert)
    {
        $columns = $insert->getRawState('columns');
        $values = $insert->getRawState('values');
        $key = array_search($this->primaryKeyField, $columns);
        if ($key !== false) {
            $this->sequenceValue = $values[$key];
        }   
        return $insert;  
    }

    /**
     * @param StatementInterface $statement
     * @param ResultInterface $result
     */
    public function postInsert(StatementInterface $statement, ResultInterface $result) {
        if ($this->sequenceValue !== null) {
            $this->tableGateway->lastInsertValue = $this->sequenceValue;
        }
    }

}
