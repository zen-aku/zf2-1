<?php

namespace DbGenerator\Adapter;

/**
 * $config['dbgenerator'] - конфигурация соединения c бд
 * 'dbgenerator' => array(
 *		'driver' => 'mysql',			- обязателный
 *		'hostname' => 'localhost',	- обязателный
 *		'database' => 'test',		
 *		'username' => 'root',
 *		'password' => '',
 *		'options' => []
 * )
 * Конфиги должны заноситься в массив конфигов соединений 
 * 
 */ 
class Adapter extends \PDO {   
	
	/**
	 * @var array  $supportDrivers
	 */
	protected $supportDrivers = array (
		'mysql',
	);

	/**
	 * объект текущегосоединения с бд
	 * @var Driver $driver; 
	 */
	protected $driver;

	/**
     * @param array $config
     */
    public function __construct(array $config) {
        					
		if ( !isset($config['driver']) )
			throw new \InvalidArgumentException(" Не задан драйвер 'driver' для соединения с бд");
		if ( !in_array($driver = strtolower($config['driver']), $this->supportDrivers ) ) 
			throw new \Exception('Текущая версия DbGenerator не поддерживает драйвер: '.$driver);
		
		// $config надо занести в объект Driver $this->driver, а также другую инфу о текущем соединении (версия бд)
		
		
    }
	
    
}
