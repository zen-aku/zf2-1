<?php

namespace Zend\Db\Query\Ddl\Constraint;

use Zend\Db\Query\ExpressionInterface;

/**
 * 
 */
interface ConstraintInterface extends ExpressionInterface {
    /**
     * 
     */
    public function getColumns();
}
