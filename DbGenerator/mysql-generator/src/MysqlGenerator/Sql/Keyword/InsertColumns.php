<?php

namespace MysqlGenerator\Sql\Keyword;

use MysqlGenerator\Adapter\AdapterInterface;

class InsertColumns extends Columns {
	
	/**
	 * @return string "( `column1`, `column2`, `column3` ... )"
	 */
	public function getSqlString(AdapterInterface $adapter = null){	
		return '('. parent::getSqlString($adapter) . ')';
	}   
}