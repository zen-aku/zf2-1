<?php

namespace Services\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Пользовательский класс, реализующий интерфейс ServiceLocatorAwareInterface,
 * благодаря чему он получает доступ к ServiceManager, внедрённом в его свойство $serviceLocator
 * Свойство $serviceLocator, сеттер и геттер реализованы в подключаемом Zend\ServiceManager\ServiceLocatorAwareTrait
 */
class ClassServiceLocatorAwareTrait implements ServiceLocatorAwareInterface {

	use ServiceLocatorAwareTrait;

	function getGreeting() {
		return __CLASS__ .'-'. $this->getServiceLocator()->get('Services\Service\GreetingService')->getGreeting();
	}
}
