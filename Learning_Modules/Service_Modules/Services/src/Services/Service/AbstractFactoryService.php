<?php
namespace Services\Service;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Cоздать объект класса $requestedName
 * Абстрактная фабрика сервиса должна быть имплементирована от AbstractFactoryInterface
 */
class AbstractFactoryService implements AbstractFactoryInterface {

	/**
	 * Реализует метод интерфейса canCreateServiceWithName(), который будет вызван в ServiceManager при обращении к этой фабрике
	 * Проверяет можно ли создать сервис с переданным именем, если true тогда вызывается метод CreateServiceWithName() для создания сервиса с переданным именем
	 * @return bool
	 */
	function canCreateServiceWithName( ServiceLocatorInterface $serviceLocator, $name, $requestedName ) {

		switch ( $requestedName ) {
			case 'simpleService1': return true;
			case 'simpleService2': return true;
			//case...
			default: return false;
		}
	}

	/**
	 * Реализует метод интерфейса createServiceWithName(), который будет вызван в ServiceManager при обращении к этой фабрике
	 * Создаёт сервис с переданным именем
	 * @return object
	 */
	function createServiceWithName( ServiceLocatorInterface $serviceLocator, $name, $requestedName ) {

		$getGreeting = $serviceLocator->get('Services\Service\GreetingService')->getGreeting();

		/*
		 * Возвращаем объект класса $requestedName, незарегистрированного в ServiceManager, но играющего роль сервиса
		 * Доступ к такому псевдосервису только через фабрику 'AbstractFactoryService', регистрируемую в ServiceManager
		 * и передаём в его конструктор результат сервиса 'Services\Service\GreetingService'->getGreeting()
		 */
		switch ( $requestedName ) {
			case 'simpleService1':
				return new SimpleService1( $getGreeting );
			case 'simpleService2':
				return new SimpleService2( $getGreeting );
			//case...
		}
	}

}