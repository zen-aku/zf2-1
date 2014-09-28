<?php

namespace Events\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use \Events\Listner\LoggingEventServiceListner;
use \Events\Listner\GreetingServiceListenerAggregate;

/**
 * Создать объект сервиса GreetingService и одновременно создать событие для его метода getGreeting()
 */
class GreetingServiceFactory implements FactoryInterface {

    /**
	 * Создать объект сервиса GreetingService и одновременно создать событие для его метода getGreeting()
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator - объект ServiceManager
	 * @return \Events\Service\GreetingService
	 */
	function createService( ServiceLocatorInterface $serviceLocator ) {
		
		$greetingService = new GreetingService();
		
		/* 1. attach( $event, Closure )
		 * Вызываем EventManager объекта $greetingService и прикрепляем в нём событию 'getGreeting' слушателя $callback-логгирование событий
		 */		
		$greetingService->getEventManager()->attach(
			'getGreeting', 
			function($event) use($serviceLocator) {
				$serviceLocator->get('Events\Service\LoggingEventService')->addEventLog($event);
			}	
		);
		
		/* 2. attach( $event, new Class-invoke )
		 * Прикрепить событию 'getGreeting' объекта инвок-класса LoggingEventServiceListner
		 */		
		$greetingService->getEventManager()->attach(		    
			'getGreeting', 
			new LoggingEventServiceListner($serviceLocator)
		);
        
        /* 3. attach( ListenerAggregateInterface )
         * Прикрепить аггрегатор событий 
         */
        $greetingService->getEventManager()->attach(new GreetingServiceListenerAggregate($serviceLocator));
        
			
		return $greetingService;
	}
	
}
