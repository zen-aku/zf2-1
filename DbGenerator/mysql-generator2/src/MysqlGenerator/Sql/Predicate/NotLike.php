<?php

namespace MysqlGenerator\Sql\Predicate;

class NotLike extends Like
{
    protected $specification = '%1$s NOT LIKE %2$s';
}
