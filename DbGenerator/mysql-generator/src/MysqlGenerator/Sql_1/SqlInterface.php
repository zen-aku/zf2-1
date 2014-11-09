<?php

namespace MysqlGenerator\Sql;

use MysqlGenerator\Adapter\AdapterInterface;

interface SqlInterface
{
    /**
     * @param AdapterInterface $adapter
     */
    public function getSqlString(AdapterInterface $adapter);
}
