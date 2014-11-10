<?php

namespace MysqlGenerator\Sql\Keyword;

use MysqlGenerator\Adapter\AdapterInterface;

class Into extends Table {
	
	const KEYWORD = 'INTO ';
	
	/**
	 * @return string "INTO `table`"
	 */
	public function getSqlString(AdapterInterface $adapter = null){	
		return self::KEYWORD . parent::getSqlString($adapter);
	}   
}