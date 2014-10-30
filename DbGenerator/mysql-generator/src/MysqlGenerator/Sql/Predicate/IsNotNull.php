<?php

namespace MysqlGenerator\Sql\Predicate;

class IsNotNull extends IsNull
{
    protected $specification = '%1$s IS NOT NULL';
}
