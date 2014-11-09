<?php

namespace MysqlGenerator\Sql\Keyword;

use MysqlGenerator\Adapter\AdapterInterface;
use MysqlGenerator\Adapter\StatementContainerInterface;

interface PreparableKeywordInterface {

    /**
     * @param AdapterInterface $adapter
     * @param StatementContainerInterface $statementContainer
     * @return void
     */
    public function getPrepareString(AdapterInterface $adapter, StatementContainerInterface $statementContainer);
}
