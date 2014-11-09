<?php

namespace MysqlGenerator\TableGateway;

use MysqlGenerator\Adapter\AdapterInterface;
use MysqlGenerator\ResultSet\ResultSet;
use MysqlGenerator\ResultSet\ResultSetInterface;
use MysqlGenerator\Sql\Sql;
use MysqlGenerator\Sql\TableIdentifier;

class TableGateway extends AbstractTableGateway
{

    /**
     * Constructor
     *
     * @param string $table
     * @param AdapterInterface $adapter
     * @param Feature\AbstractFeature|Feature\FeatureSet|Feature\AbstractFeature[] $features
     * @param ResultSetInterface $resultSetPrototype
     * @param Sql $sql
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($table, AdapterInterface $adapter, $features = null, ResultSetInterface $resultSetPrototype = null, Sql $sql = null)
    {
        if (!(is_string($table) || $table instanceof TableIdentifier)) {
            throw new Exception\InvalidArgumentException('Table name must be a string or an instance of MysqlGenerator\Sql\TableIdentifier');
        }      
        // process features
        if ($features !== null) {
            if ($features instanceof Feature\AbstractFeature) {
                $features = array($features);
            }
            if (is_array($features)) {
                $this->featureSet = new Feature\FeatureSet($features);
            } elseif ($features instanceof Feature\FeatureSet) {
                $this->featureSet = $features;
            } else {
                throw new Exception\InvalidArgumentException(
                    'TableGateway expects $feature to be an instance of an AbstractFeature or a FeatureSet, or an array of AbstractFeatures'
                );
            }
        } else {
            $this->featureSet = new Feature\FeatureSet();
        }
        $this->sql = ($sql) ?: new Sql($table);     
        if ($this->sql->getTable() != $table) {
            throw new Exception\InvalidArgumentException('The table inside the provided Sql object must match the table of this TableGateway');
        }
        // result prototype
        $this->resultSetPrototype = ($resultSetPrototype) ?: new ResultSet;
        $this->table = $table;
        $this->adapter = $adapter;
        
        $this->initialize();
    }
}
