<?php

namespace MysqlGenerator\Sql\Ddl;

use MysqlGenerator\Sql\AbstractSql;
use MysqlGenerator\Adapter\AdapterInterface;

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
     * @param AdapterInterface $adapter
     * @return string
     */
    public function getSqlString(AdapterInterface $adapter) {

        $sqls       = array();
        $parameters = array();

        foreach ($this->specifications as $name => $specification) {
            if (is_int($name)) {
                $sqls[] = $specification;
                continue;
            }
			// quoteIdentifier($name):
            $parameters[$name] = $this->{'process' . $name}($adapter, null, $sqls, $parameters);
			
            if ($specification && is_array($parameters[$name]) && ($parameters[$name] != array(array()))) {
                $sqls[$name] = $this->createSqlFromSpecificationAndParameters($specification, $parameters[$name]);
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
     * @param AdapterInterface $adapter
     * @return type
     */
    protected function processTable(AdapterInterface $adapter)
    {
        $ret = array();
        if ($this->isTemporary) {
            $ret[] = 'TEMPORARY ';
        } else {
            $ret[] = '';
        }
        $ret[] = $adapter->quoteIdentifier($this->table);
        return $ret;
    }
 
	// Из декоратора
	/**
     * @param AdapterInterface $adapter
     * @return string
     */
    protected function processColumns(AdapterInterface $adapter) {
		
        $sqls = array();
        foreach ($this->columns as $i => $column) {
            $stmtContainer = $this->processExpression($column, $adapter);
            $sql           = $stmtContainer->getSql();
            $columnOptions = $column->getOptions();

            foreach ($columnOptions as $coName => $coValue) {
                switch (strtolower(str_replace(array('-', '_', ' '), '', $coName))) {
                    case 'autoincrement':
                        $sql .= ' AUTO_INCREMENT';
                        break;
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
     * @param AdapterInterface $adapter
     * @return type
     */
    protected function processConstraints(AdapterInterface $adapter)
    {
        $sqls = array();
        foreach ($this->constraints as $constraint) {
            $sqls[] = $this->processExpression($constraint, $adapter)->getSql();
        }
        return array($sqls);
    }
}
