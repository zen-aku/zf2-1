<?php

namespace MysqlGenerator\Adapter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AdapterServiceFactory implements FactoryInterface {
	
    /**
     * Create db adapter service
     * @param ServiceLocatorInterface $serviceLocator
     * @return Adapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $config = $serviceLocator->get('Config');
        return new Adapter($config['mysqlgenerator']);
    }
}
