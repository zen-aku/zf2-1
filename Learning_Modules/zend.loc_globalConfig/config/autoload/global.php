<?php
/**
 * Здесь находятся глобальные настройки для всего проекта.
 * Они будут доступны в репозитариях, поэтому Не размещать конфидициальные настройки в этом файле!!!
 * Все конфедициальные настройки вынести в файл local.php
 */

/*
 * Настройки объекта соединения с бд Zend\Db\Adapter\Adapter
 */
return array(
	/*
	 *  Параметры соединения с бд для Zend\Db\Adapter\Adapter
	 */
	'db' => array(
		'driver' => 'pdo_mysql',
		'hostname' => 'localhost',
		'database' => 'test',		
        'charset' => 'utf8',
    ),
	/*
	 * Создание объекта соединения Zend\Db\Adapter\Adapter через фабрику 
	 * с переданными параметрами соединения (как првило в global.php - см.выше и в local.php)
	 */
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
);
