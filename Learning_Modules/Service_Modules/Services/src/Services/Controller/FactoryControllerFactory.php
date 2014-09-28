<?php

namespace Services\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Cоздать объект контроллера ServiceController с внедрённым в его свойство сервисом 'Services\Service\GreetingService'
 */
class FactoryControllerFactory implements FactoryInterface {

	/**
	 * Вернуть объект контроллера ServiceController с внедрённым в его свойство сервисом 'Services\Service\GreetingService'
	 * Для того, чтобы ServiceManager мог управлять ServiceControllerFactory , последняя должна реализовать метод createService() интерфейса FactoryInterface.
	 * createService() вызывается автоматически в ServiceManager.
	 * @param  ServiceLocatorInterface $serviceLocator - ControllerManager
     * @return ServiceController - object с внедрённым в свойство объектом класса Services\Service\GreetingService
	 */
	function createService( ServiceLocatorInterface $serviceLocator ) {
		$ctr = new FactoryController();
		/*
		 * Внедряем в свойство $greetingService объекта $ctr сервис 'Services\Service\GreetingService' путём Setter Injection
		 * ($serviceLocator->getServiceLocator()->get('Services\Service\GreetingService')) возвращает объект класса-сервиса Services\Service\GreetingService
		 * зарегистрированного в ServiceManager c помощью Module.php
		 * Только так надо внедрять в свойство контроллера объект-сервис, а не в конструкторе контроллера,
		 * потому что из конструктора контроллера ServiceManager в $serviceLocator будет ещё недоступен.
		 * Он станет доступен только после создания объекта контроллера.
		 */
		/*
		 * C помощью getServiceLocator() ControllerManager возвращает объект ServiceManager,
		 * который в свою очередь с помощью get() возвращает запрашиваемый сервис
		 */
		$ctr->setGreetingService(
			$serviceLocator->getServiceLocator()->get('Services\Service\GreetingService')
		);
		return $ctr;
	}
}