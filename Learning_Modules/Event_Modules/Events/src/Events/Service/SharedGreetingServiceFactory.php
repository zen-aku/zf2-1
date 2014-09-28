<?php

namespace Events\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use \Events\Listner\SharedEventListnerAggregate;

/**
 * Создать объект сервиса GreetingService и одновременно создать событие для его метода getGreeting()
 */
class SharedGreetingServiceFactory implements FactoryInterface {

    /**
	 * Создать объект сервиса SharedGreetingService
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator - объект ServiceManager
	 * @return \Events\Service\GreetingService
	 */
	function createService( ServiceLocatorInterface $serviceLocator ) {
        
        /* 1. attach()
         * Добавить в SharedEventManager событие с идентификатором класса и прикрепить к нему слушателя
         * Прикрепление в SharedEventManager надо вынести в отдельный сервис, вызываемый в init() или onBootstrap() класса Module(?)
         */
		$serviceLocator->get('sharedEventManager')->attach(
			'SharedGreetingService',    // идентификатор класса
			'getGreeting',              // событие (метод класса)
			function ($event) use ($serviceLocator) {
				$serviceLocator->get('Events\Service\LoggingEventService')->addEventLog($event);
			}
		);
		
        /* 2. attachAggregate()
         * Добавить в SharedEventManager аггрегатор событий SharedEventListnerAggregate()
         */
        $serviceLocator->get('sharedEventManager')->attachAggregate(new SharedEventListnerAggregate($serviceLocator));
        
        
        /*
         * Создать объект SharedGreetingService и добавить его идентификатор в его EventManager для доступа к событиям SharedEventManager c таким идентификатором
         * Если не используется фабрика, то идентификатор addIdentifiers() добавляется в том методе класса, который запускает событие trigger()
         */
        $sharedGreetingService = new SharedGreetingService();
        $sharedGreetingService->getEventManager()->addIdentifiers('SharedGreetingService');
        
		return $sharedGreetingService;
	}
	
}

