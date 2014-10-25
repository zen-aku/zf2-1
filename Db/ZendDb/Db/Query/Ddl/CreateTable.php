<?php

namespace Zend\Db\Query\Ddl;

use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Adapter\Platform\Sql92 as AdapterSql92Platform;

use Zend\Db\Query\AbstractCommandQuery;

/**
 * 
 */
class CreateTable extends AbstractCommandQuery implements DdlInterface, DdlColumnInterface {
    
    const COLUMNS     = 'columns';
    const CONSTRAINTS = 'constraints';
    const TABLE       = 'table';
    
    /**
     * @var array
     */
    protected $columns = array();

    /**
     * @var array
     */
    protected $constraints = array();

    /**
     * @var bool
     */
    protected $isTemporary = false;
    
    /**
     * @var ColumnTable
     */
    protected $columnTable = null;


    /**
     * Specifications for Sql String generation
     * @var array
     */
    protected $specifications = array(
        self::TABLE => 'CREATE %1$sTABLE %2$s (',
        self::COLUMNS  => array(
            "\n    %1\$s" => array(
                array(1 => '%1$s', 'combinedby' => ",\n    ")
            )
        ),
        self::CONSTRAINTS => array(
            "\n    %1\$s" => array(
                array(1 => '%1$s', 'combinedby' => ",\n    ")
            )
        ),
    );

    /**
     * @param string $table
     * @param bool   $isTemporary
     */
    public function __construct($table = '', $isTemporary = false) {
        if ($table) {
            $this->setTable($table);
        }
        $this->setTemporary($isTemporary);
    }

    /**
     * @param  bool $temporary
     * @return self
     */
    public function setTemporary($temporary) {
        $this->isTemporary = (bool) $temporary;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTemporary() {
        return $this->isTemporary;
    }

    /**
     * @param  Column\ColumnInterface $column
     * @return self
     */
    /*
    public function addColumn(Column\ColumnInterface $column) {
        $this->columns[] = $column;
        return $this;
    }
    */ 
    
    /**
     * Если параметр не задан, возвращает объект ColumnTable, с помощью методов которого можно добавить кoлонку
     * @param  Column\ColumnInterface $column
     * @return self|Column\ColumnTable  if $column - null return Column\ColumnTable
     */
    public function addColumn(Column\ColumnInterface $column = null) {
        if ($column === null) {
            if ($this->columnTable === null) $this->columnTable = new Column\ColumnTable($this);
            return $this->columnTable;
        }

        $this->columns[] = $column;
        return $this;
    }
     
    /**
     * @param  Constraint\ConstraintInterface $constraint
     * @return self
     */
    public function addConstraint(Constraint\ConstraintInterface $constraint) {
        $this->constraints[] = $constraint;
        return $this;
    }

    /**
     * @param  string|null $key
     * @return array
     */
    public function getRawState($key = null) {
        $rawState = array(
            self::COLUMNS     => $this->columns,
            self::CONSTRAINTS => $this->constraints,
            self::TABLE       => $this->table,
        );
        return (isset($key) && array_key_exists($key, $rawState)) ? $rawState[$key] : $rawState;
    }

    /**
     * @param  PlatformInterface $adapterPlatform
     * @return string
     */
    public function getSqlString( PlatformInterface $adapterPlatform = null ) {
        // get platform, or create default
        $adapterPlatform = ($adapterPlatform) ?: new AdapterSql92Platform;
        $sqls       = array();
        $parameters = array();

        foreach ($this->specifications as $name => $specification) {
            if (is_int($name)) {
                $sqls[] = $specification;
                continue;
            }
            $parameters[$name] = $this->{'process' . $name}(
                $adapterPlatform,
                null,
                null,
                $sqls,
                $parameters
            );
            if ($specification
                && is_array($parameters[$name])
                && ($parameters[$name] != array(array()))
            ) {
                $sqls[$name] = $this->createSqlFromSpecificationAndParameters(
                    $specification,
                    $parameters[$name]
                );
            }
            if (stripos($name, 'table') === false
                && $parameters[$name] !== array(array())
            ) {
                $sqls[] = ",\n";
            }
        }
        // remove last ,
        if (count($sqls) > 2) {
            array_pop($sqls);
        }
        $sql = implode('', $sqls) . "\n)";

        return $sql;
    }
    
    /**
     * @param PlatformInterface $adapterPlatform
     * @return array
     */
    protected function processTable(PlatformInterface $adapterPlatform = null) {
        $ret = array();
        if ($this->isTemporary) {
            $ret[] = 'TEMPORARY ';
        } else {
            $ret[] = '';
        }
        $ret[] = $adapterPlatform->quoteIdentifier($this->table);
        return $ret;
    }
    
    /**
     * @param PlatformInterface $adapterPlatform
     * @return array
     */
    protected function processColumns(PlatformInterface $adapterPlatform = null) {
        $sqls = array();
        foreach ($this->columns as $column) {
            $sqls[] = $this->processExpression($column, $adapterPlatform)->getSql();
        }
        return array($sqls);
    }

    /**
     * @param PlatformInterface $adapterPlatform
     * @return array
     */
    protected function processConstraints(PlatformInterface $adapterPlatform = null) {
        $sqls = array();
        foreach ($this->constraints as $constraint) {
            $sqls[] = $this->processExpression($constraint, $adapterPlatform)->getSql();
        }
        return array($sqls);
    }
    
}
