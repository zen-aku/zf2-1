<?php
/*
 * Примеры взыова сервисов, зарегистрированныз разными способами в ServiceManager
 */
namespace Services\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Контроллер, создвнный с помощью прямого вызова (invokable) класса контроллера
 */
class InvokableController extends AbstractActionController {

	private $invokableSrv = null;	// объект сервиса 'Services\Service\InvokableService'

	/*
	 * Благодаря extends AbstractActionController в объекты класса InvokableController
	 * в свойство serviceLocator автоматически внедряется ServiceManager и доступ ко всем его свойствам
	 * осуществляутся через $this->getServiceLocator()->get('название сервиса') вне зависимости
	 * как был зарегистрирован сервис: object, invokable, factory, callback или abstractfactory
	 */

    function indexAction() {

    	/* 1. Services
    	 * Вызов Services-сервиса 'Services\Service\ObjectService' - вызывается объект objectService из ServiceManager
    	 * Сервис 'Services\Service\ObjectService' зарегистрирован в ServiceManager как объект класса
    	 * Такая регистрация производится только при очень частом использовнии сервиса в разных классах,
    	 * а иначе объект сервиса в ServiceManager будет впустую расходовать память
    	 */
		$objectSrv = $this->getServiceLocator()->get('Services\Service\ObjectService');

		/* 2. Invokables
		 * Вызов Invokable-сервиса 'Services\Service\InvokableService' - создаётся объект на лету из ссылки на класс в ServiceManager и возвращается
		 * Сервис 'Services\Service\InvokableService' зарегистрирован в ServiceManager как Invokable-сервис (вызываемая ссылка на класс)
		 * Такая регитрация не расходует память, но расходует ресурсы процессора при частом создании такого сервиса
		 */
    	$invokableSrv = $this->getServiceLocator()->get('Services\Service\InvokableService');

		/*
		 * Если invokable-сервис используется и в других методах класса или идёт частый вызов этого метода контроллера,
		 * то лучше внедрить объект Invokable-сервиса в свойство контроллера и потом обращаться к нему,
		 * чтобы не расходовать ресурсы процессора на частое создание такого сервиса
		 */
		if ($this->invokableSrv === null)
			$this->invokableSrv = $this->getServiceLocator()->get('Services\Service\InvokableService');

		/* 3.1. Factories-object
		 * Вызов фабрики сервиса, зарегистрированной в ServiceManager как объект
		 * Аналогично Services-сервису расходует впустую память и применяется только при частых вызовах фабрики
		 */
		$factoryObject = $this->getServiceLocator()->get('Services\Service\ServiceFactory');

		/* 3.2. Factories-callback
		 * Вызов фабрики сервиса, зарегистрированной в ServiceManager как callback
		 * Тоже расходует лишнюю память на хранение Closure-объекта фабрики
		 * и поэтому применяется только при частых вызовах фабрики
		 */
		$factoryCallback = $this->getServiceLocator()->get('Services\Service\ServiceFactoryCallback');

		/* 3.3. Factories-string (invokable)
		 * Вызов фабрики сервиса, зарегистрированной в ServiceManager factories как ссылка на класс
		 * Фабрика создаётся на лету и возвращает сервис (подобно Invokable-сервису).
		 * Имеет те же недостатки, что и Invokable-сервис плюс создаётся дополнительный класс фабрики
		 */
		$factoryInvokable = $this->getServiceLocator()->get('Services\Service\ServiceFactoryInvoke');

		/* 4. Alias
		 * Вызов сервиса 'Services\Service\GreetingService' по его псевдониму 'Services\Service\aliasService'
		 */
		$alias = $this->getServiceLocator()->get('Services\Service\aliasService');

		/* 5. Abstract_factories
		 * Создание сервисов с помошью абстрактной фабрики AbstractFactoryService,
		 * зарегистрированной в ServiceManager abstract_factories
		 */
		$simpleSrv1 = $this->getServiceLocator()->get('simpleService1');
		$simpleSrv2 = $this->getServiceLocator()->get('simpleService2');

		/* 6. Initializers
		 * Вызов invokable-сервиса, проинициализировнного во время вызова с помощью моего инициализатора \
		 * Services\Service\Initializer, зарегистрированного в ServiceManager initializers (см в конфиге сервисов)
		 */
		 $initSrv = $this->getServiceLocator()->get('Services\Service\InitService');

		 /* 7. Delegators
		  * Вызов декоратора BuzzerServiceDelegator декорируемого сервиса 'Services\Service\BuzzerService'
		  * с помощью фабрики BuzzerServiceDelegatorFactory, зарегистрированной в ServiceManager delegators
		  */
		 $delegatorBuzzerSrv = $this->getServiceLocator()->get('Services\Service\BuzzerService');

		/* 8. ServiceLocatorAwareInterface
		 * Вызов сервиса, реализующего ServiceLocatorAwareInterface, благодаря чему
		 * он имеет он получает доступ к ServiceManager, внедрённом в его свойство $serviceLocator
		 */
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