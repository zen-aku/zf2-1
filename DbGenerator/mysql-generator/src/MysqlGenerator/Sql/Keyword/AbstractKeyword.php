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
	
	/**
     * Quote identifier in fragment
     * @param  string $identifier
     * @param  array $safeWords
     * @return string
     */
    public function quoteIdentifierInFragment($identifier, array $safeWords = array()) {
		
        $parts = preg_split('#([^0-9,a-z,A-Z$_])#', $identifier, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if ($safeWords) {
            $safeWords = array_change_key_case(array_flip($safeWords), CASE_LOWER);
        }
		$str = '';
        foreach ($parts as $part) {	
			if ( $safeWords && isset($safeWords[strtolower($part)]) ) {
				$part = strtoupper($part);
			}		
			elseif ( !in_array($part , [' ', '.', '*', 'AS', 'As', 'aS', 'as']) && !($safeWords && isset($safeWords[strtolower($part)])) ){
				$part = $this->quoteIdentifier($part);
			}
			$str .=  $part;
        }
        return $str;
    }
	
}
