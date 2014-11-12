<?php

namespace MysqlGenerator\Sql\Keyword;

use MysqlGenerator\Adapter\AdapterInterface;

class From extends Table {
	
	const KEYWORD = 'FROM ';
		
	/**
	 * @return string  " FROM `schema`.`table` AS `alias` "
	 */
	public function getSqlString(AdapterInterface $adapter = null) {
		return  PHP_EOL.'    ' . self::KEYWORD . parent::getSqlString();
	}
	
}
