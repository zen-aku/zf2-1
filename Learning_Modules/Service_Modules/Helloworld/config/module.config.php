<?php
return array(

    /**
     * Каталог, в котором будут храниться файлы представления (view) нашего модуля (HTML - шаблоы).
     */
	'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    /**
     * router обеспечивает доступность соответствующих компонентов для указанных маршрутов (роутеров)
     */
	'router' => array(
        // массивы с индивидуальными маршрутами, один из которых мы назвали "sayhello"
        'routes' => array(
            // массив-роутер "sayhello"
            'sayhello' => array(
                // тип разбора роутера - литеральный (побуквенное совпадение с 'route' => '/sayhello')
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    // маршрут
                    'route' => '/sayhello',
                    // вызов компонентов
                    'defaults' => array(
                        'controller' => 'Helloworld\Controller\Index',
                        'action' => 'index',
                    )
                )
            ),
        ),
    ),

    /**
     * Определяем уникальное имя для контроллера, которое будет действительно во всех модулях приложения.
     * Чтобы гарантировать его однозначность, мы указали имя модуля и обозначение контроллера.
     * Helloworld\Controller\Index теперь станет, таким образом, символическим именем для
     * нашего контроллера, которое, соответственно, будет использовано в конфигурации маршрутов.
     * Контроллеры-классы сохраняются в хранилище Controller/ControllerManager extends ServiceManager в свойствах invokableClasse[], factories[] и др в зависимости от ключа конфига
     */
    'controllers' => array(
    	/* 1,2. Прямой вызов index-контроллера
        'invokables' => array(
            'Helloworld\Controller\Index' => 'Helloworld\Controller\IndexController',
        ),
        */
    	/* 3. Вызов index-контроллера через фабрику Helloworld\Controller\IndexControllerFactory
    	 * Объект от FactoryInterface (наш IndexControllerFactory) сохряняется в Controller/ControllerManager extends ServiceManager в свойстве factories[]
    	 * После извлечении его из хранилища объект FactoryInterface запускается с помощью FactoryInterface->createService( ServiceLocatorInterface $serviceLocator )
		 */
    	/*
    	'factories' => array(
			'Helloworld\Controller\Index' => 'Helloworld\Controller\IndexControllerFactory',
		),
    	*/
    	/* 4. Вызов index-контроллера c помощью обратного 'callback' вызова фабрики
    	 * В данном случае фабрика Helloworld\Controller\IndexControllerFactory реализуется в анонимной функции.
    	 * C одной стороны это избавит нас от создания дополнительного класса IndexControllerFactory, но перегрузит код конфигураций логическим кодом
    	 * Код анонимной функции повторяет код фабрики IndexControllerFactory
    	 * Объект Closure сохряняется в Controller/ControllerManager extends ServiceManager в свойстве factories[]
    	 * После извлечении его из хранилища объект Closure запускается с помощью Closure->_invoke($serviceLocator)
    	 */
    	'factories' => array(
    		// !!! callable-функция должна возвращать объект c внедрённым сервисом из ServiceManager $serviceLocator
    		'Helloworld\Controller\Index' => function($serviceLocator) {
    			$ctr = new Helloworld\Controller\IndexController();
    			// внедряем в свойство $greetingService объекта $ctr сервис 'greetingService'
    			$ctr->setGreetingService(
    				$serviceLocator->getServiceLocator()->get('greetingService')
    			);
    			return $ctr;
    		}
    	),
    ),

	/**
	 * Регистрация классов как сервисы в хранилище классов-сервисов ServiceManager
	 */
	'service_manager' => array(
		// Данные сервисы доступны в качестве "invokable"(вызываемые), т.е. определяют класс, экземпляр которого может быть создан при необходимости.
		'invokables' => array(
			// [название_сервиса] => [директория класса, который будет сервисом]
			'greetingService' => 'Helloworld\Service\GreetingService',
		),
	)



);
