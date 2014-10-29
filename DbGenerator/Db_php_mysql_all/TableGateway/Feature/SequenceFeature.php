<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\TableGateway\Feature;

use Zend\Db\Sql\Insert;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Driver\StatementInterface;

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
