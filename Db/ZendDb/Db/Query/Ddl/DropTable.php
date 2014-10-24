<?php

namespace Zend\Db\Query\Ddl;

use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\Adapter\Platform\Sql92 as AdapterSql92Platform;
use Zend\Db\Query\AbstractCommandQuery;

/**
 * 
 */
class DropTable extends AbstractCommandQuery implements DdlInterface {
    
    /**
     * @const
     */
    const TABLE = 'table';

    /**
     * @var array
     */
    protected $specifications = array(
        self::TABLE => 'DROP TABLE IF EXISTS %1$s'
    );

    /**
     * @param string $table
     */
    public function __construct($table = '') {
        if ($table) {
            $this->setTable($table);
        }
    }

    /**
     * @param  null|PlatformInterface $adapterPlatform
     * @return string
     */
    public function getSqlString(PlatformInterface $adapterPlatform = null) {
        // get platform, or create default
        $adapterPlatform = ($adapterPlatform) ?: new AdapterSql92Platform;

        $sqls       = array();
        $parameters = array();

        foreach ($this->specifications as $name => $specification) {
            $parameters[$name] = $this->{'process' . $name}(
                $adapterPlatform,
                null,
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
     * @param PlatformInterface $adapterPlatform
     * @return type
     */
    protected function processTable(PlatformInterface $adapterPlatform = null) {
        return array($adapterPlatform->quoteIdentifier($this->table));
    }
}
