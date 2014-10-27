<?php

namespace DbGenerator\Adapter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Создать сервис DbGenerator\Adapter\Adapter и передать ему конфигурационный массив профилей соединений с базами данных.
 */
class AdapterServiceFactory implements FactoryInterface {
    /**
     * Создать сервис DbGenerator\Adapter\Adapter 
     * и передать ему конфигурационный массив профилей соединений с базами данных $config['dbgenerator'].
     * @param ServiceLocatorInterface $serviceLocator
     * @return Adapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $config = $serviceLocator->get('Config');
        return new Adapter($config['dbgenerator']);
        // return new Adapter($serviceLocator->get('Config')['dbgenerator']);
    }
}
