<?php
/*
 * Router
 * Массив роутеров инклудится в Module->getConfig() и объединяется с другими массивами общего конфига модуля 
 */
return array(
	'router' => array(
		'routes' => array(
	
			// Literal route '<module name>-<controller>-<action>'
			'zenddb-createdb-index' => array(
				'type' => 'Literal',
				'options' => array(
					//'route' => '/<module>/<controller>/<action>'
					'route' => '/zenddb/createdb',
					// вызов компонентов
					'defaults' => array(
						// '__NAMESPACE__'	=> '<module name>\Controller'
						'__NAMESPACE__'	=> 'ZendDb\Controller',
						// 'controller' => '<controller name>'
						'controller' => 'CreateDb',
						// 'action' => '<action name>'
						'action' => 'index',
					)
				)
			),
		
			// Segment route '<module name>-controller>'
			'zenddb-index' => array(
				'type'    => 'Segment',
				'options' => array(
					// route' => '/<controller name>[/:action[/:id]]
					'route'    => '/zenddb/index[/:action[/:id]]',
					'defaults' => array(
						// '__NAMESPACE__'	=> '<module name>\Controller'
						'__NAMESPACE__'	=> 'ZendDb\Controller',
						// 'controller' => '<controller name>'
						'controller' => 'Index',
						// 'action' => '<action name>'
						'action' => 'index',
					),
					// optional constraints
					'constraints' => array(
						// 'action' => '(<action name1>|<action name2>|...)',
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						// 'action' => '(index)',
						// 'id' => '<regexp param>'
						'id' => '[0-9]+',
					),
				),
			),
			

		),
	),
);
