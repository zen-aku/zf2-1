<?php
/*
 * Router
 * Массив роутеров инклудится в Module->getConfig() и объединяется с другими массивами общего конфига модуля 
 */
return array(
	'router' => array(
		'routes' => array(

			// Segment route '<module name>-controller>'
			'testmysqlgenerator-index' => array(
				'type'    => 'Segment',
				'options' => array(
					// route' => '/<controller name>[/:action[/:id]]
					'route'    => '/testmysqlgenerator/index[/:action[/:id]]',
					'defaults' => array(
						// '__NAMESPACE__'	=> '<module name>\Controller'
						'__NAMESPACE__'	=> 'TestMysqlGenerator\Controller',
						// 'controller' => '<controller name>'
						'controller' => 'Index',
						// 'action' => '<action name>'
						'action' => 'index',
					),
					// optional constraints
					'constraints' => array(
						// 'action' => '(<action name1>|<action name2>|...)',
						// 'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
						'action' => '(index)',
						// 'id' => '<regexp param>'
						'id' => '[0-9]+',
					),
				),
			),
			
			'testmysqlgenerator-ddl' => array(
				'type'    => 'Segment',
				'options' => array(
					'route'    => '/testmysqlgenerator/ddl[/:action[/:id]]',
					'defaults' => array(
						'controller' => 'TestMysqlGenerator\Controller\Ddl',
						'action' => 'index',
					),
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id' => '[0-9]+',
					),
				),
			),
			
			'testmysqlgenerator-driver' => array(
				'type'    => 'Segment',
				'options' => array(
					'route'    => '/testmysqlgenerator/driver[/:action[/:id]]',
					'defaults' => array(
						'controller' => 'TestMysqlGenerator\Controller\Driver',
						'action' => 'index',
					),
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id' => '[0-9]+',
					),
				),
			),
			
			'testmysqlgenerator-sql' => array(
				'type'    => 'Segment',
				'options' => array(
					'route'    => '/testmysqlgenerator/sql[/:action[/:id]]',
					'defaults' => array(
						'controller' => 'TestMysqlGenerator\Controller\Sql',
						'action' => 'index',
					),
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id' => '[0-9]+',
					),
				),
			),
			

		),
	),
);
