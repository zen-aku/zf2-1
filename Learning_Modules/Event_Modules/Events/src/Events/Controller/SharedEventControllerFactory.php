<?php
namespace Events\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Создать объект контроллера EventController и добавить в SharedEventManager события для этого контроллера
 */
class SharedEventControllerFactory implements FactoryInterface {
	
	/**
	 * Создать объект контроллера SharedEventController и добавить в SharedEventManager события для этого контроллера
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator - объект ControllerManager
	 * @return \Events\Controller\EventController
	 */
	function createService( ServiceLocatorInterface $serviceLocator ) {
		
		$serviceManager = $serviceLocator->getServiceLocator();
		
        /*
         * Добавить в SharedEventManager событие с идентификатором класса 'SharedEventController' и прикрепить к нему слушателя
         * Прикрепление в SharedEventManager надо вынести в отдельный сервис, вызываемый в init() или onBootstrap() класса Module(?)
         */
		$serviceManager->get('sharedEventManager')->attach(
			// Псевдоним класса - его надо прописывать в SharedEventController::getEventManager()->addIdentifiers('псевдоним')
			//'SharedEventController',
			// Указание полного имени класса по PSR-0 - такой идентификатор автоматически добавляется в сеттере контроллера	(__CLASS__) в идентификатор ивент-менеджера
			'Events\Controller\SharedEventController',
			'indexAction', 
			function ($event) use ($serviceManager) {
				$serviceManager->get('Events\Service\LoggingEventService')->addEventLog($event);
			}
		);

        /*
         * Создать объект SharedEventController и добавить его идентификатор в его EventManager для доступа к событиям SharedEventManager c таким идентификатором
         * Если не используется фабрика, то идентификатор addIdentifiers() добавляется в том методе класса, который запускает событие trigger()
         */
        $sharedEventController = new SharedEventController();
		// Если вместо PSR-0 используется псевдоним как имя класса, то надо этот псевдоним добавить как идентификатор класса 
        //$sharedEventController->getEventManager()->addIdentifiers('SharedEventController');
        
		return $sharedEventController;	
	}

}