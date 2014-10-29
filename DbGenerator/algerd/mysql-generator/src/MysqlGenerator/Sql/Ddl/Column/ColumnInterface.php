<?php

namespace MysqlGenerator\Sql\Ddl\Column;

use MysqlGenerator\Sql\ExpressionInterface;

interface ColumnInterface extends ExpressionInterface
{
    public function getName();
    public function isNullable();
    public function getDefault();
    public function getOptions();
}
