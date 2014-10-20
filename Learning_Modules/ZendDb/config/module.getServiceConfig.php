<?php
/*
 * Service Manager Config
 */
return array(

	'services' => array(

	),

	'invokables' => array(
	
	),
    
	'factories' => array(
        /*
         * Cервис sql-запросов создан тремя способами:
         *  - с помощью анонимной функции
         *  - c помощью класса-фабрики 
         *  - c помощью класса-фабрики implements AdapterAwareInterface - не работает
         */
        'Zend\Db\Sql\Sql' => function ($sm) {
            return new \Zend\Db\Sql\Sql($sm->get('Zend\Db\Adapter\Adapter'));
        },    
        //'Zend\Db\Sql\Sql' => 'ZendDb\Service\SqlFactory',  
        //'Zend\Db\Sql\Sql' => 'ZendDb\Service\SqlFactoryAdapterAware',  // не работает 
	
	),

	'aliases' => array (
		
	),

	'abstract_factories' => array (
		
	),

	'initializers' => array(
		
	),

	'delegators' => array(
		
	),


);
