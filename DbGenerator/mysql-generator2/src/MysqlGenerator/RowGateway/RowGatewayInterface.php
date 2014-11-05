<?php

namespace MysqlGenerator\RowGateway;

interface RowGatewayInterface
{
    public function save();
    public function delete();
}
