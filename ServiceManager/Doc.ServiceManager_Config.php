<?php
// namespace Zend\ServiceManager;

interface ConfigInterface {
	/**
	 * Configure service manager
	 * @param ServiceManager $serviceManager
	 * @return void
	 */
	function configureServiceManager(ServiceManager $serviceManager);
}
/**
 * С помощью этого класса заносятся из файла config\module.config.php конфигурационные данные в ServiceManager
 * Также можно получить конфигурационные данные ServiceManager,
 */
class Config implements ConfigInterface {

	$config = array();	// массив конфигурационных данных ServiceManager
	/**
	 * В конструктор передаётся массив с конфигурационными данными из 'config\module.config.php' из того ключа, которыму соответствует ServiceManager
	 * Напр. ключу 'service_manager' соотвтуствует объект класса ServiceManager, а 'controllers' - объект класса Zend\Mvc\Controller\ControllerManager, который наследует класс ServiceManager
	 */
	__construct($config = array()) { $this->config = $config; }
	// Get allow override
	getAllowOverride() return bool $this->config['allow_override'] | null
	// Get factories
	getFactories() return array $this->config['factories'] | []
	// Get abstract factories
	getAbstractFactories() return array $this->config['abstract_factories'] | []
	// Get invokables
	getInvokables() return array $this->config['invokables'] | []
	// Get services
	getServices() return array $this->config['services'] | []
	// Get aliases
	getAliases() return array $this->config['aliases'] | []
	// Get initializers
	getInitializers() return array $this->config['initializers'] | []
	// Get shared
	getShared() return array $this->config['shared'] | []
	// Get the delegator services map, with keys being the services acting as delegates, and values being the delegator factories names
	getDelegators() return array $this->config['delegators'] | []

	//Извлечение из массива $this->config конфигурационных данных в переданный ServiceManager $serviceManager
	function configureServiceManager(ServiceManager $serviceManager) {
		if (($allowOverride = $this->getAllowOverride()) !== null) {
			$serviceManager->setAllowOverride($allowOverride);
		}
		foreach ($this->getFactories() as $name => $factory) {
			$serviceManager->setFactory($name, $factory);
		}
		foreach ($this->getAbstractFactories() as $factory) {
			$serviceManager->addAbstractFactory($factory);
		}
		foreach ($this->getInvokables() as $name => $invokable) {
			$serviceManager->setInvokableClass($name, $invokable);
		}
		foreach ($this->getServices() as $name => $service) {
			$serviceManager->setService($name, $service);
		}
		foreach ($this->getAliases() as $alias => $nameOrAlias) {
			$serviceManager->setAlias($alias, $nameOrAlias);
		}
		foreach ($this->getInitializers() as $initializer) {
			$serviceManager->addInitializer($initializer);
		}
		foreach ($this->getShared() as $name => $isShared) {
			$serviceManager->setShared($name, $isShared);
		}
		foreach ($this->getDelegators() as $originalServiceName => $delegators) {
			foreach ($delegators as $delegator) {
				$serviceManager->addDelegator($originalServiceName, $delegator);
			}
		}
	}
}


// a module configuration, "module/SomeModule/config/module.config.php"
return array(
	'service_manager' => array(
		'abstract_factories' => array(
			// Valid values include names of classes implementing
			// AbstractFactoryInterface, instances of classes implementing
			// AbstractFactoryInterface, or any PHP callbacks
			'SomeModule\Service\FallbackFactory',
		),
		'aliases' => array(
			// Aliasing a FQCN to a service name
			'SomeModule\Model\User' => 'User',
			// Aliasing a name to a known service name
			'AdminUser' => 'User',
			// Aliasing to an alias
			'SuperUser' => 'AdminUser',
		),
		'factories' => array(
			// Keys are the service names.
			// Valid values include names of classes implementing
			// FactoryInterface, instances of classes implementing
			// FactoryInterface, or any PHP callbacks
			'User'     => 'SomeModule\Service\UserFactory',
			'UserForm' => function ($serviceManager) {
				$form = new SomeModule\Form\User();
				// Retrieve a dependency from the service manager and inject it!
				$form->setInputFilter($serviceManager->get('UserInputFilter'));
				return $form;
			},
		),
		'invokables' => array(
			// 'имя сервиса' => 'полное имя класса'
			'UserInputFiler' => 'SomeModule\InputFilter\User',
		),
		'services' => array(
			// 'имя сервиса' => 'экземпляр класса - объект'
			'Auth' => new SomeModule\Authentication\AuthenticationService(),
		),
		'shared' => array(
			// Usually, you'll only indicate services that should **NOT** be
			// shared -- i.e., ones where you want a different instance
			// every time.
			'UserForm' => false,
		),
	),
);


// you may eventually want to implement Zend\ModuleManager\Feature\ServiceProviderInterface
class Module {

	function getServiceConfig() {
		return array(
			'abstract_factories' => array(),
			'aliases' => array(),
			'factories' => array(),
			'invokables' => array(),
			'services' => array(),
			'shared' => array(),
		);
	}
}


















