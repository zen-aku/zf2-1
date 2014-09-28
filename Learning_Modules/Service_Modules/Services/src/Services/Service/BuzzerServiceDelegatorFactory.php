<?php

namespace Services\Service;

use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Фабрика сервиса-декоратора, которая использует класс BuzzerServiceDelegator в качестве декоратора сервиса Services\Service\BuzzerService
 * Она должна быть имплементирована от DelegatorFactoryInterface и реализовывть его метод createDelegatorWithName(),
 * который вызывается в ServiceManager при запросе get('декорируемый сервис')
 */
class BuzzerServiceDelegatorFactory implements DelegatorFactoryInterface {

	/**
	 * @see \Zend\ServiceManager\DelegatorFactoryInterface::createDelegatorWithName()
	 * Этот метод будет вызван в ServiceManager при запросе get('декорируемый сервис'), передаст самого себя
	 * и вернёт !!!объект декоратора BuzzerServiceDelegator с внедрёнными Services\Service\BuzzerService $realBuzzer и $eventManager с 'декором'
	 * $callback - Closure, которая при invoke-вызове возвращает объект деаорируемого сервиса $name, $requestedName (в нашем примере это объект класса Services\Service\BuzzerService)
	 * @return BuzzerServiceDelegator
	 */
	function createDelegatorWithName( ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback ) {

		$eventManager = $serviceLocator->get('EventManager');
		$eventManager->attach('buzz', function () { return "Stare at the art!\n"; });

		return new BuzzerServiceDelegator(call_user_func($callback), $eventManager);
	}
}