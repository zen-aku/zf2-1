<?php

namespace Events\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Пример использования событий SharedEvent.
 */
class SharedEventController extends AbstractActionController {

	
    function indexAction() {
			
		/*
		// Передать в EventManager этого класса SharedEventManager, созданный в фабрике 
		$sharedEventManager = $this->getServiceLocator()->get('Events\Service\SharedEventManagerFactory');
		$this->getEventManager()->setSharedManager($sharedEventManager);
		*/
		
		// Идентифицировать этот класс для доступа к событиям в SharedEventManager с идентификаторм класса 'SharedEventController'	
		// Лучше идентифицировать этот класс фабрике этого класса SharedEventControllerFactory
        // $this->getEventManager()->addIdentifiers('SharedEventController');
					
		// запустить callback-слушателя для события 'indexAction' (см.EventControllerFactory.php)
	    $this->getEventManager()->trigger(__FUNCTION__);

		// Вызвать сервис логгирования событий 'Events\Service\LoggingEventService'
        $loggingEventService = $this->getServiceLocator()->get('Events\Service\LoggingEventService');
		      
        // Вызвать сервисы приветствия
        $sharedGreetingService = $this->getServiceLocator()->get('Events\Service\SharedGreetingService');
        
        // Вызвать сервис подсчёта отдельных событий 'Events\Service\CountEventService'
        $countEventService = $this->getServiceLocator()->get('Events\Service\countEventService');
        
		return new ViewModel(
            array(
                'greeting' => $sharedGreetingService->getGreeting(),
                'hello' => $sharedGreetingService->getHello(),
                'bye' => $sharedGreetingService->getBye(),
				'logging' => $loggingEventService->getLog(),
                'count' => $countEventService->getCount(),
			)
		);
	}
	
	/**
	 * Пример прикрепления слушателя к данному методу-событию в Module::init()
	 * @return \Zend\View\Model\ViewModel
	 */
	function initAction() {
	
		// запустить callback-слушателя для события 'initAction' и передать Event::setTarget($this)
	    $this->getEventManager()->trigger(__FUNCTION__, $this);
		
		// Вызвать сервис логгирования событий 'Events\Service\LoggingEventService'
        $loggingEventService = $this->getServiceLocator()->get('Events\Service\LoggingEventService');

		return new ViewModel(
            array(
			'logging' => $loggingEventService->getLog(),
			)
		);
	}
	
}