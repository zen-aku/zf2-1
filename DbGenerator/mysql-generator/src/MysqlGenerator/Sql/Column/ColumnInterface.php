<?php

namespace MysqlGenerator\Sql\Column;

use MysqlGenerator\Sql\ExpressionInterface;

interface ColumnInterface extends ExpressionInterface {
	
    public function getName();
    public function isNullable();
    public function getDefault();
    public function getOptions();
}
