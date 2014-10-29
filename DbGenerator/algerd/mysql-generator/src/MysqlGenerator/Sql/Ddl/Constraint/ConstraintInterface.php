<?php

namespace MysqlGenerator\Sql\Ddl\Constraint;

use MysqlGenerator\Sql\ExpressionInterface;

interface ConstraintInterface extends ExpressionInterface
{
    public function getColumns();
}
