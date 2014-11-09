<?php

namespace MysqlGenerator\Sql\Keyword;

abstract class AbstractKeyword {
	
	/**
     * @param  string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier) {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
	
}
