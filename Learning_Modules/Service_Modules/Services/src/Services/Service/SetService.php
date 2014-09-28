<?php

namespace Services\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Сервис, осуществляющий сеттер-регистрацию сервисов
 */
class SetService implements ServiceLocatorAwareInterface {
	
	use ServiceLocatorAwareTrait;
	
	function setService() {
		// 1.Services
		$this->getServiceLocator()->setService('Services\Service\ObjectService', new ObjectService());
		
		// 2.Invokables
		$this->getServiceLocator()->setInvokableClass('Services\Service\GreetingService', 'Services\Service\GreetingService')
			 ->setInvokableClass('Services\Service\InvokableService', 'Services\Service\InvokableService')
			 ->setInvokableClass('Services\Service\InitService', 'Services\Service\InitService')
			 ->setInvokableClass('Services\Service\BuzzerService', 'Services\Service\BuzzerService')
			 ->setInvokableClass('Services\Service\ClassServiceLocatorAware', 'Services\Service\ClassServiceLocatorAware')	
			 ->setInvokableClass('Services\Service\ClassServiceLocatorAwareTrait', 'Services\Service\ClassServiceLocatorAwareTrait');	
		
		// 3.Factories
		$this->getServiceLocator()->setFactory('Services\Service\ServiceFactory', new ServiceFactory())
			->setFactory('Services\Service\ServiceFactoryInvoke', 'Services\Service\ServiceFactory')
			->setFactory('Services\Service\ServiceFactoryCallback', function( $serviceLocator ) {
					$greetingService = $serviceLocator->get('Services\Service\GreetingService');
					/*
					 * Возвращаем объект класса СallbackService, незарегистрированного в ServiceManager, но играющего роль сервиса
					* Доступ к такому псевдосервису только через эту фабрику 'Services\Service\ServiceFactoryCallback'
					* и передаём в его конструктор результат сервиса 'Services\Service\GreetingService'->getGreeting()
					*/
					return new CallbackService($greetingService->getGreeting());
				}
			);	
		
		// 4.Aliases
		$this->getServiceLocator()->setAlias('Services\Service\aliasService', 'Services\Service\GreetingService');
		
		// 5.Abstract_factories
		$this->getServiceLocator()->addAbstractFactory('Services\Service\AbstractFactoryService');
		
		// 6.Initializers
		$this->getServiceLocator()->addInitializer('Services\Service\Initializer');
		
		// 7.Delegators
		$this->getServiceLocator()->addDelegator('Services\Service\BuzzerService', 'Services\Service\BuzzerServiceDelegatorFactory');
			
	}
}

