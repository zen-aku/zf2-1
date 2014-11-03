<?php
return array(

	'service_manager' => array(
        'factories' => array(
            'MysqlGenerator\Adapter\Adapter' => 'MysqlGenerator\Adapter\AdapterServiceFactory',
        ),
    ),

);
