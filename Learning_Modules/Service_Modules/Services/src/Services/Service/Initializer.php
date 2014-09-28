<?php
namespace Services\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\InitializerInterface;

/**
 * Класс, реализуюющий InitializerInterface, будет автоматически инициализировать сервисы во время их вызова
 * Используется, например, для логгирования вызовов сервисов
 */
class Initializer implements InitializerInterface {

	/**
	 * Автоматически вызывается при вызове любого сервиса $serviceManager->get('name service')
	 * @param object $instance - объект сервиса
	 * @param ServiceLocatorInterface $serviceLocator - ServiceManager
	 */
	function initialize ($instance, ServiceLocatorInterface $serviceLocator) {

		$init = 'Initialized Service';
		// Проинициализировать все сервисы, реализующие мой интерфейс InitServiceInterface
		if ($instance instanceof InitServiceInterface) {
			// setInit() - метод моего InitServiceInterface, реализованный в сервисах
			$instance->setInit($init);
		}
	}
}