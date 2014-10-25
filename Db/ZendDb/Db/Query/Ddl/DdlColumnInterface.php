<?php

namespace Zend\Db\Query\Ddl;

/**
 * 
 */
interface DdlColumnInterface {
    /**
     * 
     * @param Column\ColumnInterface $column
     */
    public function addColumn(Column\ColumnInterface $column);    
}