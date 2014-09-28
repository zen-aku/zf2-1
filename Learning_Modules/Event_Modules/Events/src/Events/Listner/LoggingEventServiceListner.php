<?php

namespace Events\Listner;

use Zend\ServiceManager\ServiceManager;
use Zend\EventManager\EventInterface;

/**
 * Class->__invoke(). Callback-слушатель событий: 
 *      - вызвать сервис логгирования 'Events\Service\LoggingEventService' и запустить логгирование события методом addEventLog($event)
 * Такое представление Callback-слушателя в виде отдельного класса имеет ряд преимуществ:
 *      - разгружается код класса, в котором аттачится callback-слушатель к событию (EventControllerFactory.php и GreetingServiceFactory.php)
 *      - этот инвок-класс можно ипользовать в разных классах: EventControllerFactory.php и GreetingServiceFactory.php
 * Минус только один: создаётся дополнительный класс. Хотя если следовать принципам ООП, то дополнительный класс вместо функции в коде - это скорее плюс.
 * Кроме того, анонимные функции в PHP - это те же объекты Closure, поэтому создавая отдельный класс вместо анонимки мы всего лишь детально расписываем класс Closure.
 */
class LoggingEventServiceListner {
	
	/**
	 * @var ServiceManager
	 */
	private $serviceManager;
	
	/**
	 * @param ServiceManager $serviceManager
	 */
	function __construct( ServiceManager $serviceManager ) {
		$this->serviceManager = $serviceManager;
	}
	
	/**
	 * Вызвать сервис логгирования 'Events\Service\LoggingEventService' и запустить логгирование события методом addEventLog($event)
	 * @param EventInterface $event - объект события передаётся из EventManager автоматически при запуске события, к которому прикреплён объект этого класса
	 */
	function __invoke( EventInterface $event ) {
		$this->serviceManager->get('Events\Service\LoggingEventService')->addEventLog($event);
	}
    
}
