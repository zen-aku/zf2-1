<?php

namespace MysqlGenerator\Sql\Predicate;

class NotIn extends In
{
    protected $specification = '%s NOT IN %s';
}
