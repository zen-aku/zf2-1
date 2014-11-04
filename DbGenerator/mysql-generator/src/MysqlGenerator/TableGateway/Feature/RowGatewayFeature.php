<?php

namespace MysqlGenerator\TableGateway\Feature;

use MysqlGenerator\ResultSet\ResultSet;
use MysqlGenerator\RowGateway\RowGateway;
use MysqlGenerator\RowGateway\RowGatewayInterface;
use MysqlGenerator\TableGateway\Exception;

class RowGatewayFeature extends AbstractFeature
{

    /**
     * @var array
     */
    protected $constructorArguments = array();

    /**
     * @param null $primaryKey
     */
    public function __construct()
    {
        $this->constructorArguments = func_get_args();
    }

    public function postInitialize()
    {
        $args = $this->constructorArguments;

        /** @var $resultSetPrototype ResultSet */
        $resultSetPrototype = $this->tableGateway->resultSetPrototype;

        if (!$this->tableGateway->resultSetPrototype instanceof ResultSet) {
            throw new Exception\RuntimeException(
                'This feature ' . __CLASS__ . ' expects the ResultSet to be an instance of MysqlGenerator\ResultSet\ResultSet'
            );
        }

        if (isset($args[0])) {
            if (is_string($args[0])) {
                $primaryKey = $args[0];
                $rowGatewayPrototype = new RowGateway($primaryKey, $this->tableGateway->table, $this->tableGateway->adapter, $this->tableGateway->sql);
                $resultSetPrototype->setArrayObjectPrototype($rowGatewayPrototype);
            } elseif ($args[0] instanceof RowGatewayInterface) {
                $rowGatewayPrototype = $args[0];
                $resultSetPrototype->setArrayObjectPrototype($rowGatewayPrototype);
            }
        } else {
            // get from metadata feature
            $metadata = $this->tableGateway->featureSet->getFeatureByClassName('MysqlGenerator\TableGateway\Feature\MetadataFeature');
            if ($metadata === false || !isset($metadata->sharedData['metadata'])) {
                throw new Exception\RuntimeException(
                    'No information was provided to the RowGatewayFeature and/or no MetadataFeature could be consulted to find the primary key necessary for RowGateway object creation.'
                );
            }
            $primaryKey = $metadata->sharedData['metadata']['primaryKey'];
            $rowGatewayPrototype = new RowGateway($primaryKey, $this->tableGateway->table, $this->tableGateway->adapter, $this->tableGateway->sql);
            $resultSetPrototype->setArrayObjectPrototype($rowGatewayPrototype);
        }
    }
}