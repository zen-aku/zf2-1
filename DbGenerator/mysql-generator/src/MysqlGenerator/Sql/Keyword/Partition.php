<?php
namespace MysqlGenerator\Sql\Keyword;

use MysqlGenerator\Adapter\AdapterInterface;

class Partition extends Columns {
	
	const KEYWORD = 'PARTITION';
	
	/**
	 * @return string "PARTITION ( `p1`, `p2`, `p3` ... )"
	 */
	public function getSqlString(AdapterInterface $adapter = null){	
		return self::KEYWORD. ' ('. parent::getSqlString($adapter) . ')';
	} 
	
}
