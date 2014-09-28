<?php
namespace Services\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Cоздать объект СallbackService
 * Фабрика сервиса должна быть имплементирована от FactoryInterface
 */
class ServiceFactory implements FactoryInterface {

	/**
	 * Вернуть объект класса CallbackService
	 * Для того, чтобы ServiceManager мог управлять ServiceFactory , последняя должна реализовать метод createService() интерфейса FactoryInterface.
	 * createService() вызывается автоматически в ServiceManager.
	 * @param  ServiceLocatorInterface $serviceLocator - ServiceManager
	 * @return СallbackService
	 */
	function createService( ServiceLocatorInterface $serviceLocator ) {

		/*
		 * Возвращаем объект класса СallbackService, незарегистрированного в ServiceManager, но играющего роль сервиса
		 * Доступ к такому псевдосервису только через фабрику 'Services\Service\ServiceFactoryCallback', регистрируемую в ServiceManager
		 * и передаём в его конструктор результат сервиса 'Services\Service\GreetingService'->getGreeting()
		 */
		return new CallbackService( $serviceLocator->get('Services\Service\GreetingService')->getGreeting() );
	}
}