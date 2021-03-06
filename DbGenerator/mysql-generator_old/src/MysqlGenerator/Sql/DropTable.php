<?php

namespace MysqlGenerator\Sql;

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
        foreach ($this->specifications as $name => $specification) {
            $parameters[$name] = $this->{'process' . $name}($adapter);
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
     * @return array
     */
    protected function processTable(){
        return array($this->quoteIdentifier($this->table));
    }
	
}
