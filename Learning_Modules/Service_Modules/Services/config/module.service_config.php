<?php
/*
 * Service Manager Config
 */
return array(

	/* 1.Services
	 * Сервисы регистрируются в ServiceManager в виде объектов
	 * Такая регистрация производится только при очень частом использовнии сервиса в разных классах
	 */
	'services' => array(
		'Services\Service\ObjectService' => new Services\Service\ObjectService(),
	),

	/* 2.Invokables
	 * Сервисы регистрируются в ServiceManager в виде вызываемой(invokable) ссылки на класс.
	 * При частом использовании сервиса в разных методах класса лучше внедрять сервис в свойство класса
	 * при первом запросе этого сервиса
	 */
	'invokables' => array(
		'Services\Service\GreetingService' => 'Services\Service\GreetingService',
		'Services\Service\InvokableService' => 'Services\Service\InvokableService',
		'Services\Service\InitService' => 'Services\Service\InitService',
		'Services\Service\SetService' => 'Services\Service\SetService',
		// декорируемый сервис - см. 7.Delegators
		'Services\Service\BuzzerService' => 'Services\Service\BuzzerService',
		// сервис с внедрённым ServiceManager (implements ServiceLocatorAwareInterface)
		'Services\Service\ClassServiceLocatorAware' => 'Services\Service\ClassServiceLocatorAware',
		'Services\Service\ClassServiceLocatorAwareTrait' => 'Services\Service\ClassServiceLocatorAwareTrait',

	),

	/* 3.Factories
	 * Сервисы регистрируются в ServiceManager как фабрики
	 */
	'factories' => array(
		// в виде объекта класса фабрики
		'Services\Service\ServiceFactory'	=> new Services\Service\ServiceFactory(),
		// в виде ссылки на класс фабрики
		'Services\Service\ServiceFactoryInvoke' => 'Services\Service\ServiceFactory',
		// в виде callback-фабрики
		'Services\Service\ServiceFactoryCallback' => function( $serviceLocator ) {
			$greetingService = $serviceLocator->get('Services\Service\GreetingService');
			/*
			 * Возвращаем объект класса СallbackService, незарегистрированного в ServiceManager, но играющего роль сервиса
			* Доступ к такому псевдосервису только через эту фабрику 'Services\Service\ServiceFactoryCallback'
			* и передаём в его конструктор результат сервиса 'Services\Service\GreetingService'->getGreeting()
			*/
			return new Services\Service\CallbackService($greetingService->getGreeting());
		},
	),

	/* 4.Aliases
	 *  Регистрация псевдонимов сервисов <'name service'> => <'alias service'>
	 */
	'aliases' => array (
		'Services\Service\aliasService' =>'Services\Service\GreetingService',
	),

	/* 5.Abstract_factories
	 * Регистрация «резервных» фабрик, к которым будет осуществляться обращение если искомый сервис не найден.
	 * Указывается фабрика без псевдонима.
	 * Абстрактную фабрику можно добавлять в виде объекта фабрики или в виде строки-ссылки на класс реализующий AbstractFactoryInterface,
	 * что то же самое, потому что при добавлении абстрактной фабрики в хранилище ServiceManager автоматически создаётся и сохраняется объект из строки-ссылки на класс
	 * Используется для создания простых однотипных сервисов
	 */
	'abstract_factories' => array (
		// new Services\Service\AbstractFactoryService(),
		'Services\Service\AbstractFactoryService',
	),

	/* 6.Initializers
	 * Инициализаторы - автоматически инициализируют сервисы во время их вызова командой $serviceManager->get('name service')
	 * Регистрируются без псевдонима.
	 * Ob]ect InitializerInterface, string InitializerInterface, callback
	 */
	'initializers' => array(
		'Services\Service\Initializer',
		// new Services\Service\Initializer(),
		// function () { }
	),

	/* 7.Delegators
	 * Регитрирум фабрику-декоратор, которая будет вызываться при вызове сервиса с именем ключа ('Services\Service\BuzzerService' - см. 2.Invokables)
	 * Можно зарегистрировать несколько фабрик-декораторов для одного сервиса.
	 * Можно регистрировать как invokable, object или callback
	 */
	'delegators' => array(
		'Services\Service\BuzzerService' => array(
			// как invokable
			'Services\Service\BuzzerServiceDelegatorFactory',
			// как объект
			//'Services\Service\BuzzerService' => new Services\Service\BuzzerServiceDelegatorFactory(),
			// как callback
			//'Services\Service\BuzzerService' => function() { }
		),
	),


);
