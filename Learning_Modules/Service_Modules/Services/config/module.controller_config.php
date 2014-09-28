<?php
/*
 * Controllers
 */
return array(

	// Прямой вызов контроллера
	'invokables' => array(
		'Services\Controller\Invokable' => 'Services\Controller\InvokableController',
		'Services\Controller\SetService' => 'Services\Controller\SetServiceController',
	),

	// Вызов контроллера через фабрику
	'factories' => array(

		/*
		 * Вызов контроллера Factory через фабрику Services\Controller\FcatoryControllerFactory
		 * Объект от FactoryInterface (наш FactoryControllerFactory) сохряняется в Controller/ControllerManager extends ServiceManager в свойстве factories[]
		 * После извлечении его из хранилища объект FactoryInterface запускается с помощью FactoryInterface->createService( ServiceLocatorInterface $serviceLocator )
		 */
		'Services\Controller\Factory' => 'Services\Controller\FactoryControllerFactory',

		/*
		 * Вызов контроллера Callback c помощью вызова 'callback'-фабрики
		 * В данном случае фабрика Services\Controller\FactoryControllerFactory реализуется в анонимной функции.
		 * C одной стороны это избавит нас от создания дополнительного класса FactoryControllerFactory, но перегрузит код конфигураций логическим кодом
		 * Объект Closure сохряняется в Controller/ControllerManager extends ServiceManager в свойстве factories[]
		 * После извлечении его из хранилища объект Closure запускается с помощью Closure->_invoke($serviceLocator)
		 * !!! callable-функция должна возвращать объект(в данном случае контроллера) c внедрённым сервисом из ServiceManager $serviceLocator
		 */
		'Services\Controller\Callback' => function( $serviceLocator ) {
			$ctr = new Services\Controller\CallbackController();
			// внедряем в свойство $greetingService объекта $ctr сервис 'Services\Service\GreetingService'
			$ctr->setGreetingService(
					$serviceLocator->getServiceLocator()->get('Services\Service\GreetingService')
			);
			return $ctr;
		},

	),

);
