<?php

namespace MysqlGenerator\Sql\Constraint;

use MysqlGenerator\Sql\ExpressionInterface;

interface ConstraintInterface extends ExpressionInterface {
	
    public function getColumns();
}
