<?php

namespace MysqlGenerator\Sql;

use MysqlGenerator\Adapter\Driver\DriverInterface;

interface SqlInterface
{
    /**
     * @param DriverInterface $driver
     */
    public function getSqlString(DriverInterface $driver);
}
