<?php

namespace MysqlGenerator\RowGateway;

use MysqlGenerator\Adapter\Adapter;
use MysqlGenerator\Sql\Sql;

class RowGateway extends AbstractRowGateway{

    /**
     * @param array $primaryKeyColumn
     * @param string|\MysqlGenerator\Sql\TableIdentifier $table
     * @param Adapte $adapter
     * @param Sql $sql
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($primaryKeyColumn, $table, Adapter $adapter, Sql $sql = null) { 
        
        $this->sql = ($sql) ?: new Sql($table);
        if ($this->sql->getTable() !== $table) {
            throw new Exception\InvalidArgumentException('The Sql object provided does not have a table that matches this row object');
        }
        $this->table = $table;
        $this->adapter = $adapter;  
        $this->primaryKeyColumn = empty($primaryKeyColumn) ? null : (array) $primaryKeyColumn;
        $this->initialize();          
    }
        
}
