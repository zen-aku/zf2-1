<?php

namespace Zend\Db\Query\Ddl\Column;

use Zend\Db\Query\ExpressionInterface;

/**
 * 
 */
interface ColumnInterface extends ExpressionInterface
{
    /**
     * 
     */
    public function getName();
    
    /**
     * 
     */
    public function isNullable();
    
    /**
     * 
     */
    public function getDefault();
    
    /**
     * 
     */
    public function getOptions();
}
