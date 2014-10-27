<?php
return array(
    
    /*
	 *  Параметры соединения с бд для Zend\Db\Adapter\Adapter
	 */
	'dbgenerator' => array(
		'driver' => 'mysql',
		'hostname' => 'localhost',
		'database' => 'test',		
		'charset' => 'utf8',
		'username' => 'root',     // надо перенести в глобальный local.php или в module.getConfig.local.php модуля
		'password' => '',
	),
        		  
    'service_manager' => array(
        'factories' => array(
            'DbGenerator\Adapter\Adapter' => 'DbGenerator\Adapter\AdapterServiceFactory',
        ),
    ),
	
);
