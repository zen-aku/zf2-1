<?php
/*
 * Пример создания контроллера с помощью callback-фабрики контроллера в controller_config
 * с одновременной Setter-инъекцией сервиса в свойство контроллера
 *
 * Какой смысл внедрения конкретного сервиса в свойство при создании контроллера из фабрики?
 * Может лучше внедрять сервис в свойство прямо в конструкторе?  Нет.
 * ServiceManager внедряется в свойство контроллера serviceLocator после создания объекта контроллера
 * и доступ к сервисам из serviceLocator будет возможен только после создания объекта контроллера, а не во время его создания.
 * Поэтому вызвать в конструкторе контроллера  ServiceManager из свойства serviceLocator,
 * чтобы запросить у него сервис для внедрения в свойство, нельзя.
 * Чтобы внедрить сервис в свойство контроллера во время создания контроллера и применяют фабрики контроллеров,
 * в которых сначала создаётся объект контроллера, потом внедряется в его свойство сервис и затем возвращается объект
 * контроллера с внедрённым в его свойство конкретным сервисом.
 */
namespace Services\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Контроллер создаётся с помощью callback-фабрики в конфиге
 */
class CallbackController extends AbstractActionController {

	/**
	 * Объект сервис-класса (Services\Service\GreetingService), зарегистрированного в ServiceManager
	 * Внедряется при создании объекта этого контроллера с помощью callback-фабрики
	 * @var object
	 */
	private $greetingService;	// Services\Service\GreetingService

	/**
	 * Вызывается из callback-фабрики для внедрения объекта класса-сервиса $service в контроллер CallbackController
	 * @param  $service - объект класса-сервиса
	 */
	function setGreetingService( $service ) {
		$this->greetingService = $service;
	}

	/**
	 * Вывод после вызова сервиса из свойства $this->greetingService
	 */
	function indexAction() {
		return new ViewModel(
				array('greeting' => $this->greetingService->getGreeting())
		);
	}

}