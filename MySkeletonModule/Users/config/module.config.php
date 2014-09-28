<?php
return array(
    'controllers' => array(
        'invokables' => array(
			// '<module namespace>\Controller\Hello' => '<module namespace>\Controller\HelloController'
            'Users\Controller\Users' => 'Users\Controller\UsersController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'users' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/users[/:action]',
					'constraints' => array(
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        // 'controller' => '<module name>\Controller\Hello'
                        'controller'    => 'Users\Controller\Users',
                        'action'        => 'index',
                    ),		
                ),
			
            ),
			/* Точное литеральное указание роутера
			// route-name: '<module name>-<controller>-<action>'
			'users-users-foo' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/users/foo',
                    'defaults' => array(
                        // 'controller' => '<module name>\Controller\Hello'
                        'controller'    => 'Users\Controller\Users',
                        'action'        => 'foo',
                    ),		
                ),
            ),
			*/	
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Users' => __DIR__ . '/../view',
        ),
    ),
);
