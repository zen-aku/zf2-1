<?php
/*
 * Реализация сервиса с помощью фабрики
 * фабрика классов может быть использована как для создания сервиса, так и для создания контроллера
 */
namespace Helloworld\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Внедряет в IndexController сервис 'greetingService'с помощью автоматически вызываемого метода createService():
 * возвращает объект IndexController с внедрённым объектом класса Helloworld\Service\GreetingService
 */
class IndexControllerFactory implements FactoryInterface {
	/**
	 * Для того, чтобы ServiceManager мог управлять IndexControllerFactory , последняя должна
	 * реализовать метод createService() интерфейса FactoryInterface. Затем этот метод вызывается в ServiceManager.
	 * @param  ServiceLocatorInterface $serviceLocator
     * @return IndexController object с внедрённым объектом класса Helloworld\Service\GreetingService
	 */
	//!!! createService() должен возвращать объект c внедрённым сервисом из ServiceManager $serviceLocator
	function createService( ServiceLocatorInterface $serviceLocator ) {
		$ctr = new IndexController();
		/**
		 * В метод фабрик createService() всегда передается тот "ServiceManager", который отвечает за создание этого сервиса,
		 * соответственно, в данном случае это – ControllerLoader (вызываемый фреймворком автоматически),
		 * который зарезервирован для создания контроллеров. Однако, у него, в свою очередь, нет никакого
		 * доступа к GreetingService, подготовленному нами заранее, и доступному только из "центрального ServiceManager"
		 * (действительно, GreetingService – это, в конечном счете, не контроллер). Для того, чтобы, несмотря на это, сделать сервисы центрального
		 * ServiceManager доступными, ControllerLoader получает доступ к ServiceManager с помощью getServiceLocator()
		 *
		 * Внедряется зависимость, Setter Injection
		 * ($serviceLocator->getServiceLocator()->get('greetingService')) возвращает объект класса-сервиса Helloworld\Service\GreetingService
		 * зарегистрированного в ServiceManager c помощью config\module.config.php или в Module.php
		 */
		// внедряем в свойство $greetingService объекта $ctr сервис 'greetingService'
		$ctr->setGreetingService(
			$serviceLocator->getServiceLocator()->get('greetingService')
		);
		return $ctr;
	}
}