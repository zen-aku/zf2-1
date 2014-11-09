<?php

namespace MysqlGenerator\Sql\Keyword;

class Into extends Table {
	
	const KEYWORD = 'INTO ';
	
	/**
	 * @return string "INTO `table`"
	 */
	public function getString(){	
		return self::KEYWORD . parent::getString();
	}   
}