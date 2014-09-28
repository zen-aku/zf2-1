<?php

namespace Helloworld\Service;

use Zend\EventManager\EventManagerInterface;
/**
 * Класс-сервис, который регистрируется в ServiceManager
 * c помощью config\module.config.php или в Module.php
 * Данный класс-сервис осуществляет вывод приветствия в зависимости от времени
 * GreetingService->getGreeting() может запускать соответствующее событие 'getGreeting'
 * Данный пример не использует встроенный механизм инъекции EventManager с помощью EventManagerAwareInterface
 * (см. Doc.EventManager_Injection.php)
 * Данные примеры содержат опасные упрощения: $this->eventManager вместо $this->getEventManager()
 * и getEventManager() реализован без проверки существования объекта EventManager в private $eventManager.
 */
class GreetingService {

    private $eventManager;      // EventManager

	// получить приветствие в зависимости от времени
	function getGreeting() {

	    $this->eventManager->trigger('getGreeting');

		if ( date("H") <= 11 ) {
 			return "Good morning, world!";
 		} else if ( date("H") > 11 && date("H") < 17 ) {
	 		return "Hello, world!";
	 	} else {
		 	return "Good evening, world!";
	 	}
	}

	function getEventManager() {
		return $this->eventManager;
	}

    function setEventManager( EventManagerInterface $em ) {
		$this->eventManager = $em;
	}
}

/**
 * Если мы используем SharedServiceManager в Helloworld\Service\GreetingServiceFactory.
 * Нужно обеспечить, чтобы в этом показательном примере, непосредственно в сервисе перед запуском события,
 * соответствующий EventManager почувствовал ответственность за этот "идентификатор".
 * Это можно сделать, используя метод addIdentifiers() в Helloworld\Service\GreetingService
 */
/*
use Zend\EventManager\EventManagerAwareInterface;

class GreetingService implements EventManagerAwareInterface {

    private $eventManager;

    function getGreeting() {

        $this->eventManager->addIdentifiers('GreetingService');
    	$this->eventManager->trigger('getGreeting');

    	if ( date("H") <= 11 ) {
    		return "Good morning, world!";
    	} else if ( date("H") > 11 && date("H") < 17 ) {
    		return "Hello, world!";
    	} else {
    		return "Good evening, world!";
    	}
    }

    function getEventManager() {
        return $this->eventManager;
    }

    function setEventManager( EventManagerInterface $em ) {
    	$this->eventManager = $em;
    }
}
*/