<?php

namespace Events\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use \Events\Listner\LoggingEventServiceListner;

/**
 * Создать объект контроллера EventController и одновременно создать событие для его метода 'indexAction'
 */
class EventControllerFactory implements FactoryInterface {
	
	/**
	 * Создать объект контроллера EventController и одновременно создать событие для его метода 'indexAction'
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator - объект ControllerManager
	 * @return \Events\Controller\EventController
	 */
	function createService( ServiceLocatorInterface $serviceLocator ) {
        
		$serviceManager = $serviceLocator->getServiceLocator();
		$eventController = new EventController();
		
		/* 1. attach($event, Closure)
		 * Вызываем EventManager объекта $eventController и прикрепляем в нём событию 'indexAction' слушателя $callback - логгирование событий
		 */
		$eventController->getEventManager()->attach(
			'indexAction',  
			$callback = function($event) use($serviceManager) {
				$serviceManager->get('Events\Service\LoggingEventService')->addEventLog($event);
			}	
		);
		
        /* 2. attach($event, new Class-invoke)
		 * Прикрепить событию 'getGreeting' объект инвок-класса LoggingEventServiceListner
		 */	        
		$eventController->getEventManager()->attach(		    
			'indexAction', 
			new LoggingEventServiceListner($serviceManager)
		);
                   
		return $eventController;		
	}
	
}