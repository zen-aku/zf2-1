<?php

namespace Events\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Пример использования событий.
 */
class EventController extends AbstractActionController {

    function indexAction() {
				
		// запустить callback-слушателя для события 'indexAction' (см.EventControllerFactory.php)
        $param = array(1,2,3,4,5);
	    $this->getEventManager()->trigger(__FUNCTION__, __CLASS__, $param);
		     	
        // Вызвать сервисы приветствия
        $greetingService = $this->getServiceLocator()->get('Events\Service\GreetingService');    
          
        // Вызвать сервис логгирования событий 'Events\Service\LoggingEventService'
        $loggingEventService = $this->getServiceLocator()->get('Events\Service\LoggingEventService');
        
        // Вызвать сервис подсчёта отдельных событий 'Events\Service\CountEventService'
        $countEventService = $this->getServiceLocator()->get('Events\Service\countEventService');
          
    	return new ViewModel(
            array(
                // вызвать события 'getGreeting', 'getHello' и 'getBye', которые запустят callback-слушателей прикреплённых к ним (в GreetingServiceFactory.php) 
                'greeting' => $greetingService->getGreeting(),
                'hello' => $greetingService->getHello(),
                'bye' => $greetingService->getBye(),
                'logging' => $loggingEventService->getLog(),
                'count' => $countEventService->getCount(),
            )
    	);
    }
    
    
    /**
     * Вызов вида нестандартного пути View из карты 'template_map' view_manager
     * @return \Zend\View\Model\ViewModel
     */
    function showAction() {
        
		// вызов вида из 'template_map' для 'events/event/show'
        return new ViewModel(
        
        );
    }
    
}