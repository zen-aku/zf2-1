<?php

return array(
    // 'полное имя класса' => 'абсолютный путь к файлу с классом'
    'ZendDb\Module' => __DIR__.'/Module.php',
	
	// controllers
    'ZendDb\Controller\IndexController' => __DIR__.'/src/ZendDb/Controller/IndexController.php',
	'ZendDb\Controller\CreateDbController' => __DIR__.'/src/ZendDb/Controller/CreateDbController.php',
    'ZendDb\Controller\AdapterController' => __DIR__.'/src/ZendDb/Controller/AdapterController.php',
	'ZendDb\Controller\SqlController' => __DIR__.'/src/ZendDb/Controller/SqlController.php',
    'ZendDb\Controller\DdlController' => __DIR__.'/src/ZendDb/Controller/DdlController.php',
    'ZendDb\Controller\MydbController' => __DIR__.'/src/ZendDb/Controller/MydbController.php',
    
	// services
    'ZendDb\Service\SqlFactory' => __DIR__.'/src/ZendDb/Service/SqlFactory.php',
    'ZendDb\Service\SqlFactoryAdapterAware' => __DIR__.'/src/ZendDb/Service/SqlFactoryAdapterAware.php',
	
    
    
);