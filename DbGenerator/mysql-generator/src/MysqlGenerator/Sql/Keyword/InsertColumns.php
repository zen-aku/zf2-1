<?php

namespace MysqlGenerator\Sql\Keyword;

class InsertColumns extends Columns {
	
	/**
	 * @return string "( `column1`, `column2`, `column3` ... )"
	 */
	public function getString(){	
		return '('. parent::getString() . ')';
	}   
}