<?php

namespace MysqlGenerator\Adapter\Driver\Feature;

interface DriverFeatureInterface {
    
    /**
     * Add feature
     * @param string $name
     * @param mixed $feature
     * @return DriverFeatureInterface
     */
    public function addFeature($name, $feature);

    /**
     * Get feature
     * @param $name
     * @return mixed|false
     */
    public function getFeature($name);
}
