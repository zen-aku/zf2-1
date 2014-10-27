<?php
return array(
    
    /*
	 *  Параметры соединения с бд для Zend\Db\Adapter\Adapter
	 */
	'dbgenerator' => array(
        'test' => array (
            'driver' => 'pdo_mysql',
            'hostname' => 'localhost',
            'database' => 'test',		
            'charset' => 'utf8',
            //'username' => 'root',     // в local.php
            //'password' => '',
        ),
        		
    ),
    
    
    'service_manager' => array(
        'factories' => array(
            'DbGenerator\Adapter\Adapter' => 'DbGenerator\Adapter\AdapterServiceFactory',
        ),
    ),
	
);
