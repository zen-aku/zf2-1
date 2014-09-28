<?php

return array(
    // 'полное имя класса' => 'абсолютный путь к файлу с классом'
    'Services\Module' => __DIR__.'/Module.php',

	// controllers
    'Services\Controller\InvokableController' 		=> __DIR__.'/src/Services/Controller/InvokableController.php',
	'Services\Controller\FactoryController' 		=> __DIR__.'/src/Services/Controller/FactoryController.php',
	'Services\Controller\FactoryControllerFactory' 	=> __DIR__.'/src/Services/Controller/FactoryControllerFactory.php',
	'Services\Controller\CallbackController' 		=> __DIR__.'/src/Services/Controller/CallbackController.php',
	'Services\Controller\SetServiceController' 		=> __DIR__.'/src/Services/Controller/SetServiceController.php',

	
	// services
	'Services\Service\GreetingService' 	=> __DIR__.'/src/Services/Service/GreetingService.php',
	'Services\Service\ObjectService' 	=> __DIR__.'/src/Services/Service/ObjectService.php',
	'Services\Service\InvokableService' => __DIR__.'/src/Services/Service/InvokableService.php',
	'Services\Service\CallbackService' 	=> __DIR__.'/src/Services/Service/CallbackService.php',
	'Services\Service\ServiceFactory' 	=> __DIR__.'/src/Services/Service/ServiceFactory.php',
	'Services\Service\AbstractFactoryService' => __DIR__.'/src/Services/Service/AbstractFactoryService.php',
	'Services\Service\SimpleService1' 	=> __DIR__.'/src/Services/Service/SimpleService1.php',
	'Services\Service\SimpleService2' 	=> __DIR__.'/src/Services/Service/SimpleService2.php',
	'Services\Service\Initializer' 		=> __DIR__.'/src/Services/Service/Initializer.php',
	'Services\Service\InitServiceInterface' => __DIR__.'/src/Services/Service/InitServiceInterface.php',
	'Services\Service\InitService' 		=> __DIR__.'/src/Services/Service/InitService.php',
	'Services\Service\BuzzerService' 	=> __DIR__.'/src/Services/Service/BuzzerService.php',
	'Services\Service\BuzzerServiceDelegator' => __DIR__.'/src/Services/Service/BuzzerServiceDelegator.php',
	'Services\Service\BuzzerServiceDelegatorFactory' => __DIR__.'/src/Services/Service/BuzzerServiceDelegatorFactory.php',
	'Services\Service\ClassServiceLocatorAware' => __DIR__.'/src/Services/Service/ClassServiceLocatorAware.php',
	'Services\Service\ClassServiceLocatorAwareTrait' => __DIR__.'/src/Services/Service/ClassServiceLocatorAwareTrait.php',
	'Services\Service\SetService'		=> __DIR__.'/src/Services/Service/SetService.php',
	
);