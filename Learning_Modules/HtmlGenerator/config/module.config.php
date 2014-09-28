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

			// Segment route '<module name>-<controller name>-<action name>'
			'htmlgenerator-index' => array(
				'type'    => 'Literal',
				'options' => array(
					// route' => '/<modulename>/<controller name>/<action name>
					'route'    => '/htmlgenerator',
					'defaults' => array(
						'__NAMESPACE__'	=> 'HtmlGenerator\Controller',
						'controller' => 'Index',
						'action' => 'index',
					),					
				),
			),

		),
	),

);
