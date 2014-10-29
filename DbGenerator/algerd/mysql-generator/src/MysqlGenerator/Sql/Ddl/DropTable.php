<?php

namespace MysqlGenerator\Sql\Ddl;

use MysqlGenerator\Sql\AbstractSql;
use MysqlGenerator\Adapter\Driver\DriverInterface;

class DropTable extends AbstractSql implements SqlInterface
{
    const TABLE = 'table';

    /**
     * @var array
     */
    protected $specifications = array(
        self::TABLE => 'DROP TABLE IF EXISTS %1$s'
    );

    /**
     * @var string
     */
    protected $table = '';

    /**
     * @param string $table
     */
    public function __construct($table = '')
    {
        $this->table = $table;
    }

    /**
     * @param
     * @return string
     */
    public function getSqlString(DriverInterface $driver){
        
        $sqls       = array();
        $parameters = array();

        foreach ($this->specifications as $name => $specification) {
            $parameters[$name] = $this->{'process' . $name}(
                $driver,
                null,
                $sqls,
                $parameters
            );

            if ($specification && is_array($parameters[$name])) {
                $sqls[$name] = $this->createSqlFromSpecificationAndParameters(
                    $specification,
                    $parameters[$name]
                );
            }
        }

        $sql = implode(' ', $sqls);
        return $sql;
    }

    /**
     * @param DriverInterface $driver
     * @return type
     */
    protected function processTable(DriverInterface $driver)
    {
        return array($driver->quoteIdentifier($this->table));
    }
}
