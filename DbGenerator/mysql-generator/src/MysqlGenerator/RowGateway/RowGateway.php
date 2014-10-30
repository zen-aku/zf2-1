<?php

namespace MysqlGenerator\RowGateway;

use MysqlGenerator\Adapter\Adapter;
use MysqlGenerator\Sql\Sql;

class RowGateway extends AbstractRowGateway
{

    /**
     * Constructor
     *
     * @param string $primaryKeyColumn
     * @param string|\MysqlGenerator\Sql\TableIdentifier $table
     * @param Adapter|Sql $adapterOrSql
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($primaryKeyColumn, $table, $adapterOrSql = null)
    {

        // setup primary key
        $this->primaryKeyColumn = empty($primaryKeyColumn) ? null : (array) $primaryKeyColumn;

        // set table
        $this->table = $table;

        // set Sql object
        if ($adapterOrSql instanceof Sql) {
            $this->sql = $adapterOrSql;
        } elseif ($adapterOrSql instanceof Adapter) {
            $this->sql = new Sql($adapterOrSql, $this->table);
        } else {
            throw new Exception\InvalidArgumentException('A valid Sql object was not provided.');
        }

        if ($this->sql->getTable() !== $this->table) {
            throw new Exception\InvalidArgumentException('The Sql object provided does not have a table that matches this row object');
        }

        $this->initialize();
    }
}
