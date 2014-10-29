<?php

namespace Zend\Db\Adapter;

/**
 * @property Driver\DriverInterface $driver
 */
interface AdapterInterface {
    
    /**
     * @return Driver\DriverInterface
     */
    public function getDriver();

}
