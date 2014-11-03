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
	 *  Параметры соединения с бд для MysqlGenerator\Adapter\Adapter
	 */
	'mysqlgenerator' => array(
		'hostname' => 'localhost',
		'database' => 'test',		
		'charset' => 'utf8',
		'username' => 'root',     // надо перенести в глобальный local.php или в module.getConfig.local.php модуля
		'password' => '',
	),

);
