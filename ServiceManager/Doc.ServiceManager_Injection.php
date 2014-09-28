<?php
/*
 * По-умолчанию Zend Framework 2 MVC регистрирует инициализатора (какой инициализатор и где он находится?),
 * который инъекцирует экземпляр ServiceManager (ServiceLocatorInterface $serviceLocator) в любой класс,
 * реализующий Zend\ServiceManager\ServiceLocatorAwareInterface.
 * Поэтому, если есть необходимость в доступе к ServiceManager из какого-то пользовательского класса,
 * то необходимо имплементрировать его от ServiceLocatorAwareInterface и реализовать его методы setServiceLocator() и getServiceLocator()
 * ServiceLocatorAwareInterface - можно перевести как 'знающий о ServiceLocator'
 */
//namespace Zend\ServiceManager;
interface ServiceLocatorAwareInterface {
	/**
	 * Set service locator
	 * @param ServiceLocatorInterface $serviceLocator
	 */
	function setServiceLocator(ServiceLocatorInterface $serviceLocator);
	/**
	 * Get service locator
	 * @return ServiceLocatorInterface
	*/
	function getServiceLocator();
}

/*
 * Простой пример. Наследники этого класса будут иметь доступ к ServiceManager через унаследованный метод $this->getServiceLocator()
 * Контроллеры модулей надо наследовать от AbstractActionController, который в свою очередь наследуется от AbstractController!!!,
 * а тот имплементирует ServiceLocatorAwareInterface и реализует его методы.
 * И таким образом в контроллеры инъекцируется ServiceManager и они имеют доступ к нему либо напрямую через свойство $this->serviceLocator (что ненадо делать)
 * или через метод $this->getServiceLocator() (что правильнее)
 */

// use Zend\ServiceManager\ServiceLocatorAwareInterface;
// use Zend\ServiceManager\ServiceLocatorInterface
// use Zend\ServiceManager\ServiceManager

class MyClass implements ServiceLocatorAwareInterface {
	protected $serviceLocator;

	function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->serviceLocator = $serviceLocator;
	}

	function getServiceLocator() {
		if (null === $this->serviceLocator)
			$this->setServiceLocator(new ServiceManager());
		return $this->serviceLocator;
	}

	function action() {
		// ...
		$servuce = $this->getServiceLocator()->get('название сервиса');
		// ...
	}
}

/*
 * В ZF2 есть трейт, реализующий ServiceLocatorAwareInterface.
 * И таким образом, упрощается реализация ServiceLocatorAwareInterface в собственном классе.
 */

//namespace Zend\ServiceManager;
trait ServiceLocatorAwareTrait {
	/**
	 * @var ServiceLocatorInterface
	 */
	protected $serviceLocator = null;
	/**
	 * Set service locator
	 * @param ServiceLocatorInterface $serviceLocator
	 * @return mixed
	 */
	function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->serviceLocator = $serviceLocator;
		return $this;
	}
	/**
	 * Get service locator
	 * @return ServiceLocatorInterface
	 */
	function getServiceLocator() {
		return $this->serviceLocator;
	}
}

/*
 * И наш пример с трейтом
 */

// use Zend\ServiceManager\ServiceLocatorAwareInterface;
class MyClass implements ServiceLocatorAwareInterface {

	use Zend\ServiceManager\ServiceLocatorAwareTrait;

	function action() {
		// ...
		$service = $this->getServiceLocator()->get('название сервиса');
		// ...
	}
}

