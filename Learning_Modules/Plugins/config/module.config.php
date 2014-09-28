<?php
return array(

	/*
	 * View
	 */
	'view_manager' => array(
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
		'template_map' => array(
			 'layout/EventsLayout' => __DIR__ . '/../view/layout/events-layout.phtml',
		),
	),

	/*
	 * Router
	 */
	'router' => array(
		'routes' => array(

			// Segment route '<module name>'
		'plugins' => array(
			'type'    => 'Segment',
			'options' => array(
				'route'    => '/plugins/index[/:action[/:arg]]',
				'defaults' => array(
					'__NAMESPACE__'	=> 'Plugins\Controller',
					'controller' => 'Index',
					'action' => 'index',
				),
				// optional constraints
				'constraints' => array(
					// 'action' => '(<action name1>|<action name2>|...)',
					// 'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					// 'arg' => '<regexp param>'
					'arg' => '[0-9]+',
				),
			),
		),
			
		'plugins-messenger' => array(
			'type'    => 'Segment',
			'options' => array(
				'route'    => '/plugins/messenger[/:action[/:arg]]',
				'defaults' => array(
					'__NAMESPACE__'	=> 'Plugins\Controller',
					'controller' => 'Messenger',
					'action' => 'index',
				),
				// optional constraints
				'constraints' => array(
					// 'action' => '(<action name1>|<action name2>|...)',
					// 'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					// 'arg' => '<regexp param>'
					'arg' => '[0-9]+',
				),
			),
		),	


		),
	),

);
