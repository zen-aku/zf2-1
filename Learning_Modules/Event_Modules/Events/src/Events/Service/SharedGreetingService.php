<?php

namespace Events\Service;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Класс-сервис, который регистрируется в ServiceManager
 */
class SharedGreetingService implements EventManagerAwareInterface  {

    use EventManagerAwareTrait;

	/**
     * Получить приветствие в зависимости от времени
     * @return string Строка приветствия в зависимости от времени суток
     */
	function getGreeting() {

        // Задать идентификатор класса для SharedEventManager. Как правило, его прописывают в фабрике этого сервиса, чтобы он работал при работе со всеми методами класса
        //$this->getEventManager()->addIdentifiers('SharedGreetingService');
        // запустить callback-слушателя для события 'getGreeting' (см.GreetingServiceFactory.php)
	    $this->getEventManager()->trigger(__FUNCTION__);

		if ( date("H") <= 11 ) {
 			return "Good morning, world!";
 		} else if ( date("H") > 11 && date("H") < 17 ) {
	 		return "Hello, world!";
	 	} else {
		 	return "Good evening, world!";
	 	}
	}
    
    function getHello() {
        
        // запустить callback-слушателя для события 'getHello' (см.GreetingServiceFactory.php)
	    $this->getEventManager()->trigger(__FUNCTION__);
        
        return "Hello!";
    }
    
    function getBye() {
        
        // запустить callback-слушателя для события 'getBye' (см.GreetingServiceFactory.php)
	    $this->getEventManager()->trigger(__FUNCTION__);
        
        return "Bye!";
    }
    
}