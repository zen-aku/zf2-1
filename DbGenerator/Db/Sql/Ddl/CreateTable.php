<?php

namespace Zend\Db\Sql\Ddl;

use Zend\Db\Sql\AbstractSql;
use Zend\Db\Adapter\Driver\DriverInterface;

class CreateTable extends AbstractSql implements SqlInterface
{
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
     * @var string
     */
    protected $table = '';

    /**
     * @param string $table
     * @param bool   $isTemporary
     */
    public function __construct($table = '', $isTemporary = false)
    {
        $this->table = $table;
        $this->setTemporary($isTemporary);
    }

    /**
     * @param  bool $temporary
     * @return self
     */
    public function setTemporary($temporary)
    {
        $this->isTemporary = (bool) $temporary;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTemporary()
    {
        return $this->isTemporary;
    }

    /**
     * @param  string $name
     * @return self
     */
    public function setTable($name)
    {
        $this->table = $name;
        return $this;
    }

    /**
     * @param  Column\ColumnInterface $column
     * @return self
     */
    public function addColumn(Column\ColumnInterface $column)
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * @param  Constraint\ConstraintInterface $constraint
     * @return self
     */
    public function addConstraint(Constraint\ConstraintInterface $constraint)
    {
        $this->constraints[] = $constraint;
        return $this;
    }

    /**
     * @param  string|null $key
     * @return array
     */
    public function getRawState($key = null)
    {
        $rawState = array(
            self::COLUMNS     => $this->columns,
            self::CONSTRAINTS => $this->constraints,
            self::TABLE       => $this->table,
        );

        return (isset($key) && array_key_exists($key, $rawState)) ? $rawState[$key] : $rawState;
    }

    /**
     * @param DriverInterface $driver
     * @return string
     */
    public function getSqlString(DriverInterface $driver) {

        $sqls       = array();
        $parameters = array();

        foreach ($this->specifications as $name => $specification) {
            if (is_int($name)) {
                $sqls[] = $specification;
                continue;
            }

            $parameters[$name] = $this->{'process' . $name}(
                $driver,
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
     * @param DriverInterface $driver
     * @return type
     */
    protected function processTable(DriverInterface $driver)
    {
        $ret = array();
        if ($this->isTemporary) {
            $ret[] = 'TEMPORARY ';
        } else {
            $ret[] = '';
        }

        $ret[] = $driver->quoteIdentifier($this->table);
        return $ret;
    }
 
	// Из декоратора
	/**
     * @param DriverInterface $driver
     * @return string
     */
    protected function processColumns(DriverInterface $driver) {
		
        $sqls = array();
        foreach ($this->columns as $i => $column) {
            $stmtContainer = $this->processExpression($column, $driver);
            $sql           = $stmtContainer->getSql();
            $columnOptions = $column->getOptions();

            foreach ($columnOptions as $coName => $coValue) {
                switch (strtolower(str_replace(array('-', '_', ' '), '', $coName))) {
                    case 'identity':
                    case 'serial':
                    case 'autoincrement':
                        $sql .= ' AUTO_INCREMENT';
                        break;
                    /*
                    case 'primary':
                    case 'primarykey':
                        $sql .= ' PRIMARY KEY';
                        break;
                    case 'unique':
                    case 'uniquekey':
                        $sql .= ' UNIQUE KEY';
                        break;
                    */
                    case 'comment':
                        $sql .= ' COMMENT \'' . $coValue . '\'';
                        break;
                    case 'columnformat':
                    case 'format':
                        $sql .= ' COLUMN_FORMAT ' . strtoupper($coValue);
                        break;
                    case 'storage':
                        $sql .= ' STORAGE ' . strtoupper($coValue);
                        break;
                }
            }
            $stmtContainer->setSql($sql);
            $sqls[$i] = $stmtContainer;
        }
        return array($sqls);
    }
	
    /**
     * @param DriverInterface $driver
     * @return type
     */
    protected function processConstraints(DriverInterface $driver)
    {
        $sqls = array();
        foreach ($this->constraints as $constraint) {
            $sqls[] = $this->processExpression($constraint, $driver)->getSql();
        }
        return array($sqls);
    }
}
