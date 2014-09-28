<?php

return array(
    // 'полное имя класса' => 'абсолютный путь к файлу с классом'
    'Events\Module' => __DIR__.'/Module.php',
	
	// controllers
    'Events\Controller\EventController'			=> __DIR__.'/src/Events/Controller/EventController.php',
    'Events\Controller\EventControllerFactory'	=> __DIR__.'/src/Events/Controller/EventControllerFactory.php',
	'Events\Controller\SharedEventController'	=> __DIR__.'/src/Events/Controller/SharedEventController.php',
	'Events\Controller\SharedEventControllerFactory' => __DIR__.'/src/Events/Controller/SharedEventControllerFactory.php',
	
	// services
    'Events\Service\LoggingEventService'		=> __DIR__.'/src/Events/Service/LoggingEventService.php',
    'Events\Service\GreetingService'			=> __DIR__.'/src/Events/Service/GreetingService.php',
    'Events\Service\GreetingServiceFactory'		=> __DIR__.'/src/Events/Service/GreetingServiceFactory.php',
    'Events\Service\SharedEventManagerFactory'	=> __DIR__.'/src/Events/Service/SharedEventManagerFactory.php',
	'Events\Service\CountEventService'          => __DIR__.'/src/Events/Service/CountEventService.php',
	'Events\Service\SharedGreetingService'		=> __DIR__.'/src/Events/Service/SharedGreetingService.php',
    'Events\Service\SharedGreetingServiceFactory'=> __DIR__.'/src/Events/Service/SharedGreetingServiceFactory.php',
  
    
	// listners
	'Events\Listner\LoggingEventServiceListner' => __DIR__.'/src/Events/Listner/LoggingEventServiceListner.php',
	'Events\Listner\GreetingServiceListnerAggregate' => __DIR__.'/src/Events/Listner/GreetingServiceListnerAggregate.php',
	'Events\Listner\SharedEventListnerAggregate'=> __DIR__.'/src/Events/Listner/SharedEventListnerAggregate.php',
    'Events\Listner\SharedListnerAggregateTrait'=> __DIR__.'/src/Events/Listner/SharedListnerAggregateTrait.php',
       
);