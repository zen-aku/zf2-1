<?php

namespace MysqlGenerator\TableGateway\Exception;

use MysqlGenerator\Exception;

class RuntimeException extends Exception\InvalidArgumentException implements ExceptionInterface
{
}
