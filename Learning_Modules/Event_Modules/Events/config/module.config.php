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
            /*
             * Нестандартный путь вида view: вызов other.phtml для экшена showAction()
             * <модуль>/<контроллер>/<экшен>  => __DIR__ . '/../<путь к шаблон.phtml>'
             */
            'events/event/show' => __DIR__ . '/../view/events/event/other.phtml', 
		),	
	),

	/*
	 * Router
	 */
	'router' => array(
		'routes' => array(

			// Literal route '<module name>-<controller>-<action>'
			'events-event-index' => array(
				'type' => 'Literal',
				'options' => array(
					//'route' => '/<module>/<controller>/<action>'
					'route' => '/events/event',
					'defaults' => array(
						'__NAMESPACE__'	=> 'Events\Controller',
						'controller' => 'Event',
						'action' => 'index',
					)
				)
			),
            
            // Literal route '<module name>-<controller>-<action>'
			'events-event-show' => array(
				'type' => 'Literal',
				'options' => array(
					//'route' => '/<module>/<controller>/<action>'
					'route' => '/events/event/show',
					'defaults' => array(
						'__NAMESPACE__'	=> 'Events\Controller',
						'controller' => 'Event',
						'action' => 'show',
					)
				)
			),

			'events-sharedevent-index' => array(
				'type' => 'Literal',
				'options' => array(
					//'route' => '/<module>/<controller>/<action>'
					'route' => '/events/sharedevent',
					'defaults' => array(
						'__NAMESPACE__'	=> 'Events\Controller',
						'controller' => 'SharedEvent',
						'action' => 'index',
					)
				)
			),
			
			'events-sharedevent-init' => array(
				'type' => 'Literal',
				'options' => array(
					//'route' => '/<module>/<controller>/<action>'
					'route' => '/events/sharedevent/init',
					'defaults' => array(
						'__NAMESPACE__'	=> 'Events\Controller',
						'controller' => 'SharedEvent',
						'action' => 'init',
					)
				)
			),
			
		),
	),

);
