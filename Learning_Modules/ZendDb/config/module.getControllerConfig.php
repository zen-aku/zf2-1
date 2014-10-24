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
        'ZendDb\Controller\Ddl' => 'ZendDb\Controller\DdlController',
        'ZendDb\Controller\Mydb' => 'ZendDb\Controller\MydbController',
	),

	'factories' => array(

	),

);
