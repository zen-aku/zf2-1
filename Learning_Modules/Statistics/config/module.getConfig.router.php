<?php

return array(
	'router' => array(
		'routes' => array(

			'statistics' => array(
				'type'    => 'Segment',
				'options' => array(
					'route'    => '/statistics[/[:controller[/[:action[/[:slug[/]]]]]]]',
					'defaults' => array(
						'controller' => 'Statistics\Controller\Index',
						'action' => 'index',
					),
					'constraints' => array(
						'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'slug' => '[a-zA-Z][a-zA-Z0-9_-]*',
					),
				),
			),
			

		),
	),
);
