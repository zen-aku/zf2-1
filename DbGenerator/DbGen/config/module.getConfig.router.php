<?php
/*
 * Router
 * Массив роутеров инклудится в Module->getConfig() и объединяется с другими массивами общего конфига модуля 
 */
return array(
	'router' => array(
		'routes' => array(

			// Segment route '<module name>-controller>'
			'dbgen-index' => array(
				'type'    => 'Segment',
				'options' => array(
					// route' => '/<controller name>[/:action[/:id]]
					'route'    => '/dbgen/index[/:action[/:id]]',
					'defaults' => array(
						// '__NAMESPACE__'	=> '<module name>\Controller'
						'__NAMESPACE__'	=> 'DbGen\Controller',
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
			

		),
	),
);
