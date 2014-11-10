<?php

namespace MysqlGenerator\Sql\Keyword;

use MysqlGenerator\Sql\SqlInterface;

abstract class AbstractKeyword implements SqlInterface {
	
	/**
     * @param  string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier) {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
	
}
