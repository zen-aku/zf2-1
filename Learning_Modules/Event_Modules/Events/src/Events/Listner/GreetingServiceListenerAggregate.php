<?php

namespace Events\Listner;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Агрeгатор событий для EventManager
 */
class GreetingServiceListenerAggregate implements ListenerAggregateInterface {

	use ListenerAggregateTrait;

    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;
    
    /** 
     * @param ServiceLocatorInterface $serviceLocator
     */
    function __construct( ServiceLocatorInterface  $serviceLocator ) {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @param EventManagerInterface $eventManager
     */
	function attach( EventManagerInterface $eventManager ) {
        
        $serviceLocator = $this->serviceLocator;
        
		$this->listeners = array(
            $eventManager->attach( 
                array('getHello', 'getBye'), 
                new LoggingEventServiceListner($serviceLocator)
            ),
            $eventManager->attach( 
                array('getGreeting', 'getHello', 'getBye'),
                function($event) use($serviceLocator) {
                    $serviceLocator->get('Events\Service\CountEventService')->count();
                }
            )    
		);
	}
    
}
