<?php

namespace Zend\Db\Sql;

/*
 * 
 */
class Table {

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string | Schema - класс схимы бд, в которой находится эта таблица ???
     */
    protected $schema;

    /**
     * @param string $table
     * @param string $schema
     */
    public function __construct($table, $schema = null) {
        $this->table = $table;
        $this->schema = $schema;
    }

    /**
     * @param string $table
     */
    public function setTable($table) {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * @return bool
     */
    public function hasSchema() {
        return ($this->schema != null);
    }

    /**
     * @param $schema
     */
    public function setSchema($schema) {
        $this->schema = $schema;
    }

    /**
     * @return null|string
     */
    public function getSchema() {
        return $this->schema;
    }

}
