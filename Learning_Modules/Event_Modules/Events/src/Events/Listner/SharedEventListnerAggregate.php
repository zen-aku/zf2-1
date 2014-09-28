<?php

namespace Events\Listner;

use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Агрeгатор событий для SharedEventManager
 */
class SharedEventListnerAggregate implements SharedListenerAggregateInterface {
    
    use SharedListnerAggregateTrait;
    
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
     * 
     * @param SharedEventManagerInterface $sharedEventManager
     */
    function attachShared( SharedEventManagerInterface $sharedEventManager ) {
        
        $serviceLocator = $this->serviceLocator;
        
		$this->listeners['SharedGreetingService'] = array(
            
            $sharedEventManager->attach( 
                'SharedGreetingService', 
                array('getGreeting', 'getHello', 'getBye'), 
                function($event) use($serviceLocator) {
                    $serviceLocator->get('Events\Service\CountEventService')->count();
                }
            ),
            $sharedEventManager->attach( 
                'SharedGreetingService', 
                array('getHello', 'getBye'), 
                new LoggingEventServiceListner($serviceLocator)
            ),        
                          		
		);
	}
    
}
