<?php
namespace MysqlGenerator\Sql;

use MysqlGenerator\Adapter\StatementContainerInterface;
use MysqlGenerator\Adapter\AdapterInterface;

abstract class AbstractDml implements SqlInterface, PreparableSqlInterface {
	
	/**
     * @param AdapterInterface $adapter
     * @return string
     */
    public function getSqlString(AdapterInterface $adapter) {
		$sqlString = PHP_EOL;
		foreach ($this->keywords as $keyword) {
			if ( $keyword instanceof SqlInterface ) {
				$sqlString .= $keyword->getSqlString($adapter). ' ';
			}
			elseif ($keyword) {
				$sqlString .= $keyword . ' ';
			}
		}
		return $sqlString;	
	}
 
	/**
     * @param  AdapterInterface $adapter
     * @param  StatementContainerInterface $statementContainer
     * @return void
     */
	public function prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer) {
		$sqlString = PHP_EOL;
		foreach ($this->keywords as $keyword) {
			if ( $keyword instanceof PreparableSqlInterface ) {
				$sqlString .= $keyword->prepareStatement($adapter, $statementContainer)->getSql(). ' ';
			}		
			elseif ( $keyword instanceof SqlInterface) {
				$sqlString .= $keyword->getSqlString($adapter). ' ';
			}
			elseif ($keyword) {
				$sqlString .= $keyword . ' ';
			}
		}
		return $statementContainer->setSql($sqlString);
	}
	
}

