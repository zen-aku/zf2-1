<?php
/*
 * Service Manager Config
 */
return array(

	'services' => array(
		
	),

	'invokables' => array(
		'Events\Service\LoggingEventService' => 'Events\Service\LoggingEventService',
        'Events\Service\CountEventService' => 'Events\Service\CountEventService',
	),

	'factories' => array(
		'Events\Service\GreetingService' => 'Events\Service\GreetingServiceFactory',
		'Events\Service\SharedEventManagerFactory' => 'Events\Service\SharedEventManagerFactory',
        'Events\Service\SharedGreetingService' => 'Events\Service\SharedGreetingServiceFactory',
	
	),

	'aliases' => array (
		
	),

	'abstract_factories' => array (
		
	),

	'initializers' => array(
		
	),

	'delegators' => array(
		
	),


);
