<?php

namespace Events\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\EventManager\SharedEventManager;

/**
 * Создать объект SharedEventManager и прикрепить ему события
 */
class SharedEventManagerFactory implements FactoryInterface {
	
	/**
	 * Создать объект SharedEventManager и прикрепить ему события
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return SharedEventManager
	 */
	function createService( ServiceLocatorInterface $serviceLocator ) {
		
		$sharedEventManager = new SharedEventManager();
		
		$sharedEventManager->attach(
			'EventController', 
			'indexAction', 
			function ($event) use ($serviceLocator) {
				$serviceLocator->get('Events\Service\LoggingEventService')->addEventLog($event);
			}
		);

		return $sharedEventManager;
	}
}