<?php

namespace MysqlGenerator\Sql;

use MysqlGenerator\Adapter\AdapterInterface;

class AlterTable extends AbstractSql implements SqlInterface
{
    const ADD_COLUMNS      = 'addColumns';
    const ADD_CONSTRAINTS  = 'addConstraints';
    const CHANGE_COLUMNS   = 'changeColumns';
    const DROP_COLUMNS     = 'dropColumns';
    const DROP_CONSTRAINTS = 'dropConstraints';
    const TABLE            = 'table';

    /**
     * @var array
     */
    protected $addColumns = array();

    /**
     * @var array
     */
    protected $addConstraints = array();

    /**
     * @var array
     */
    protected $changeColumns = array();

    /**
     * @var array
     */
    protected $dropColumns = array();

    /**
     * @var array
     */
    protected $dropConstraints = array();

    /**
     * Specifications for Sql String generation
     * @var array
     */
    protected $specifications = array(
        self::TABLE => "ALTER TABLE %1\$s\n",
        self::ADD_COLUMNS  => array(
            "%1\$s" => array(
                array(1 => 'ADD COLUMN %1$s', 'combinedby' => ",\n")
            )
        ),
        self::CHANGE_COLUMNS  => array(
            "%1\$s" => array(
                array(2 => 'CHANGE COLUMN %1$s %2$s', 'combinedby' => ",\n"),
            )
        ),
        self::DROP_COLUMNS  => array(
            "%1\$s" => array(
                array(1 => 'DROP COLUMN %1$s', 'combinedby' => ",\n"),
            )
        ),
        self::ADD_CONSTRAINTS  => array(
            "%1\$s" => array(
                array(1 => 'ADD %1$s', 'combinedby' => ",\n"),
            )
        ),
        self::DROP_CONSTRAINTS  => array(
            "%1\$s" => array(
                array(1 => 'DROP CONSTRAINT %1$s', 'combinedby' => ",\n"),
            )
        )
    );

    /**
     * @var string
     */
    protected $table = '';

    /**
     * @param string $table
     */
    public function __construct( $table = '' ) {
		$this->table = $table;
    }

    /**
     * @param  string $name
     * @return self
     */
    public function setTable($name){
        $this->table = $name;
        return $this;
    }

    /**
     * @param  Column\ColumnInterface $column
     * @return self
     */
    public function addColumn(Column\ColumnInterface $column){
        $this->addColumns[] = $column;
        return $this;
    }

    /**
     * @param  string $name
     * @param  Column\ColumnInterface $column
     * @return self
     */
    public function changeColumn($name, Column\ColumnInterface $column){
        $this->changeColumns[$name] = $column;
        return $this;
    }

    /**
     * @param  string $name
     * @return self
     */
    public function dropColumn($name){
        $this->dropColumns[] = $name;
        return $this;
    }

    /**
     * @param  string $name
     * @return self
     */
    public function dropConstraint($name){
        $this->dropConstraints[] = $name;
        return $this;
    }

    /**
     * @param  Constraint\ConstraintInterface $constraint
     * @return self
     */
    public function addConstraint(Constraint\ConstraintInterface $constraint){
        $this->addConstraints[] = $constraint;
        return $this;
    }

    /**
     * @param  string|null $key
     * @return array
     */
    public function getRawState($key = null){
        $rawState = array(
            self::TABLE => $this->table,
            self::ADD_COLUMNS => $this->addColumns,
            self::DROP_COLUMNS => $this->dropColumns,
            self::CHANGE_COLUMNS => $this->changeColumns,
            self::ADD_CONSTRAINTS => $this->addConstraints,
            self::DROP_CONSTRAINTS => $this->dropConstraints,
        );
        return (isset($key) && array_key_exists($key, $rawState)) ? $rawState[$key] : $rawState;
    }

    /**
     * @param AdapterInterface $adapter
     * @return string
     */
    public function getSqlString(AdapterInterface $adapter){   
		
        $sqls = array();
        foreach ($this->specifications as $name => $specification) {
            $parameters[$name] = $this->{'process' . $name}($adapter);
            if ($specification && is_array($parameters[$name]) && ($parameters[$name] != array(array()))) {
                $sqls[$name] = $this->createSqlFromSpecificationAndParameters($specification, $parameters[$name]);
            }
            if (stripos($name, 'table') === false && $parameters[$name] !== array(array())) {
                $sqls[] = ",\n";
            }
        }
        // remove last ,\n
        array_pop($sqls);
        $sql = implode('', $sqls);
        return $sql;
    }

    /** 
     * @return type
     */
    protected function processTable(){
        return array($this->quoteIdentifier($this->table));
    }

    /**
     * @param AdapterInterface $adapter
     * @return type
     */
    protected function processAddColumns(AdapterInterface $adapter) {
        $sqls = array();
        foreach ($this->addColumns as $column) {
            $sqls[] = $this->processExpression($column, $adapter)->getSql();
        }
        return array($sqls);
    }

    /**
     * @param AdapterInterface $adapter
     * @return type
     */
    protected function processChangeColumns(AdapterInterface $adapter){
        $sqls = array();
        foreach ($this->changeColumns as $name => $column) {
            $sqls[] = array(
                $this->quoteIdentifier($name),
                $this->processExpression($column, $adapter)->getSql()
            );
        }
        return array($sqls);
    }

    /**
     * @return type
     */
    protected function processDropColumns(){
        $sqls = array();
        foreach ($this->dropColumns as $column) {
             $sqls[] = $this->quoteIdentifier($column);
        }
        return array($sqls);
    }
    
    /**
     * @param AdapterInterface $adapter
     * @return array
     */
    protected function processAddConstraints(AdapterInterface $adapter){
        $sqls = array();
        foreach ($this->addConstraints as $constraint) {
            $sqls[] = $this->processExpression($constraint, $adapter);
        }
        return array($sqls);
    }

    /**
     * @return array
     */
    protected function processDropConstraints(){
        $sqls = array();
        foreach ($this->dropConstraints as $constraint) {
            $sqls[] = $this->quoteIdentifier($constraint);
        }
        return array($sqls);
    }
}
