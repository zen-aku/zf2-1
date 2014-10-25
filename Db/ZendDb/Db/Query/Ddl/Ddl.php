<?php

namespace Zend\Db\Query\Ddl;

use Zend\Db\Query\AbstractQuery;

/**
 * 
 */
class Ddl extends AbstractQuery {
  
    /**
     * @param string $table
     * @return DropTable 
     */
    public function dropTable( $table = null ) {
        $this->errorSetTable($table);
        return new DropTable(($table) ?: $this->table);
    }
    
    /**
     * @param string $table
     * @return CreateTable
     */
    public function createTable( $table = null ) {
        $this->errorSetTable( $table );
        return new CreateTable(($table) ?: $this->table);
    }
	
	/**
     * @param string $table
     * @return AlterTable
     */
    public function alterTable( $table = null ) {
        $this->errorSetTable( $table );
        return new AlterTable(($table) ?: $this->table);
    }        
    
}
