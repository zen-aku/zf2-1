<?php

namespace Events\Service;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Класс-сервис, который регистрируется в ServiceManager
 */
class GreetingService implements EventManagerAwareInterface  {

    use EventManagerAwareTrait;

	/**
     * Получить приветствие в зависимости от времени
     * @return string Строка приветствия в зависимости от времени суток
     */
	function getGreeting() {

        $param = array('param1', 'param2');
        
        // запустить callback-слушателя для события 'getGreeting' (см.GreetingServiceFactory.php)
	    $this->getEventManager()->trigger(__FUNCTION__, __CLASS__, $param );

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