<?php

namespace Zend\Db\Sql;

use Zend\Db\Adapter\Driver\DriverInterface;

interface SqlInterface
{
    /**
     * @param DriverInterface $driver
     */
    public function getSqlString(DriverInterface $driver);
}
