<?php

namespace MysqlGenerator\Sql\Ddl;

use MysqlGenerator\Sql\AbstractSql;
use MysqlGenerator\Adapter\AdapterInterface;

class DropTable extends AbstractSql implements SqlInterface {
	
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
    public function __construct($table = ''){
        $this->table = $table;
    }

    /**
     * @param AdapterInterface $adapter
     * @return string
     */
    public function getSqlString(AdapterInterface $adapter){
        
        $sqls       = array();
        $parameters = array();

        foreach ($this->specifications as $name => $specification) {
            $parameters[$name] = $this->{'process' . $name}(
                $adapter,
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
     * @param AdapterInterface $adapter
     * @return array
     */
    protected function processTable(AdapterInterface $adapter){
        return array($adapter->quoteIdentifier($this->table));
    }
	
}
