<?php
/*
 * Controllers
 */
return array(

	'invokables' => array(
		'ZendDb\Controller\Index' => 'ZendDb\Controller\IndexController',
		'ZendDb\Controller\CreateDb' => 'ZendDb\Controller\CreateDbController',
        'ZendDb\Controller\Adapter' => 'ZendDb\Controller\AdapterController',
        'ZendDb\Controller\Sql' => 'ZendDb\Controller\SqlController',
	),

	'factories' => array(

	),

);
