<?php

namespace MysqlGenerator\Adapter\Driver\Feature;

use MysqlGenerator\Adapter\AdapterInterface;

abstract class AbstractFeature {

    /**
     * @var AdapterInterface
     */
    protected $adapter = null;

    /**
     * Set adapter
     * @param AdapterInterface $adapter
     * @return void
     */
    public function setDriver(AdapterInterface $adapter){
        $this->adapter = $adapter;
    }

    /**
     * Get name
     * @return string
     */
    abstract public function getName();

}
