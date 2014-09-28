<?php
/*
 * Controllers
 */
return array(

	'invokables' => array(
		
	),

	'factories' => array(
        'Events\Controller\Event' => 'Events\Controller\EventControllerFactory',
		'Events\Controller\SharedEvent' => 'Events\Controller\SharedEventControllerFactory'
	),

);
