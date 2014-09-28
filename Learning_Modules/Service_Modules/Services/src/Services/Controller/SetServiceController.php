<?php
namespace Services\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Примеры вызова сервисов, зарегистрированных в ServiceManager c помощью сеттеров в сервисе
 * Для тестирования этого примера надо закомментировать подключение service_config в классе ModuleConfig
 */
class SetServiceController extends AbstractActionController {

	private $invokableSrv = null;
	
	function IndexAction() {

		// Регистрируем сервис, который в свою очередь регистрируют остальные сервисы
		$this->getServiceLocator()->setInvokableClass('Services\Service\SetService', 'Services\Service\SetService');
		/*
		 * Вызов сервиса, осуществляющего сеттер-регистрацию сервисов
		 */
		$this->getServiceLocator()->get('Services\Service\SetService')->setService();

		
		// 1. Services	
		$objectSrv = $this->getServiceLocator()->get('Services\Service\ObjectService');
		// 2. Invokables
		$invokableSrv = $this->getServiceLocator()->get('Services\Service\InvokableService');
		if ($this->invokableSrv === null)
			$this->invokableSrv = $this->getServiceLocator()->get('Services\Service\InvokableService');
		// 3. Factories
		$factoryObject = $this->getServiceLocator()->get('Services\Service\ServiceFactory');	
		$factoryCallback = $this->getServiceLocator()->get('Services\Service\ServiceFactoryCallback');
		$factoryInvokable = $this->getServiceLocator()->get('Services\Service\ServiceFactoryInvoke');
		// 4. Alias
		$alias = $this->getServiceLocator()->get('Services\Service\aliasService');
		// 5. Abstract_factories
		$simpleSrv1 = $this->getServiceLocator()->get('simpleService1');
		$simpleSrv2 = $this->getServiceLocator()->get('simpleService2');
		// 6. Initializers
		$initSrv = $this->getServiceLocator()->get('Services\Service\InitService');
		// 7. Delegators
		$delegatorBuzzerSrv = $this->getServiceLocator()->get('Services\Service\BuzzerService');
		// 8. ServiceLocatorAwareInterface
		$serviceAware = $this->getServiceLocator()->get('Services\Service\ClassServiceLocatorAware');
		$serviceAwareTrait = $this->getServiceLocator()->get('Services\Service\ClassServiceLocatorAwareTrait');
		
		
		return new ViewModel(
			array(
    			'object' => $objectSrv->getInfo(),
    			'invokable' => $invokableSrv->getInfo(),
    			'invokable_inject' => $this->invokableSrv->getInfo(),
    			'factoryObject' => $factoryObject->getInfo(),
    			'factoryCallback' => $factoryCallback->getInfo(),
    			'factoryInvokable' => $factoryInvokable->getInfo(),
    			'alias' => $alias->getGreeting(),
    			'simple1' => $simpleSrv1->getInfo(),
    			'simple2' => $simpleSrv2->getInfo(),
    			'init' => $initSrv->getInfo(),
    			'delegator'	=> $delegatorBuzzerSrv->buzz(),
    			'serviceAware'=> $serviceAware->getGreeting(),
    			'serviceAwareTrait'=> $serviceAwareTrait->getGreeting(),
			)
		);
	}


}