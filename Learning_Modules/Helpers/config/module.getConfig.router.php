<?php
/*
 * Router
 * Массив роутеров инклудится в Module->getConfig() и объединяется с другими массивами общего конфига модуля 
 */
return array(
	'router' => array(
		'routes' => array(
            
			// Segment route '<module name>-<controller name>'
			'helpers-index' => array(
				'type'    => 'Segment',
				'options' => array(
					// route' => '/<modulename>/<controller name>[/:action[/:id]]
					'route'    => '/helpers/index[/:action[/:id]]',
					'defaults' => array(
						'__NAMESPACE__'	=> 'Helpers\Controller',
						'controller' => 'Index',
						'action' => 'index',
					),
					// optional constraints
					'constraints' => array(
						// 'action' => '(<action name1>|<action name2>|...)',
						 'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						//'action' => '(index)',
						// 'id' => '<regexp param>'
						'id' => '[0-9]+',
					),
				),
			),
            
			'helpers-views' => array(
				'type'    => 'Literal',
				'options' => array(
					'route'    => '/helpers/views',
					'defaults' => array(
                        '__NAMESPACE__'	=> 'Helpers\Controller',
						'controller' => 'Views',
						'action' => 'index',
					),					
				),
			),
            
			
		),
		
	)
);
