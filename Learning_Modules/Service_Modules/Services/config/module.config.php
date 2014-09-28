<?php
return array(

	/*
	 * View
	 */
	'view_manager' => array(
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),

	/*
	 * Router
	 */
	'router' => array(
		'routes' => array(

			// Literal route '<module name>-<controller>-<action>'
			'services-invokable-index' => array(
				'type' => 'Literal',
				'options' => array(
					//'route' => '/<controller>/<action>'
					'route' => '/invokable',
					// вызов компонентов
					'defaults' => array(
						// '__NAMESPACE__'	=> '<module name>\Controller'
						'__NAMESPACE__'	=> 'Services\Controller',
						// 'controller' => '<controller name>'
						'controller' => 'Invokable',
						// 'action' => '<action name>'
						'action' => 'index',
					)
				)
			),

			'services-factory-index' => array(
				'type' => 'Literal',
				'options' => array(
					'route' => '/factory',
					'defaults' => array(
						'__NAMESPACE__'	=> 'Services\Controller',
						'controller' => 'Factory',
						'action' => 'index',
					)
				)
			),

			'services-callback-index' => array(
				'type' => 'Literal',
				'options' => array(
					'route' => '/callback',
					'defaults' => array(
						'__NAMESPACE__'	=> 'Services\Controller',
						'controller' => 'Callback',
						'action' => 'index',
					)
				)
			),
			
			'services-setservice-index' => array(
				'type' => 'Literal',
				'options' => array(
					'route' => '/setservice',
					'defaults' => array(
						'__NAMESPACE__'	=> 'Services\Controller',
						'controller' => 'SetService',
						'action' => 'index',
					)
				)
			),
			

		),
	),

);
