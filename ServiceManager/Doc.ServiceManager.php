<?php
//namespace Zend\ServiceManager;
/**
 * Service locator interface
 */
interface ServiceLocatorInterface {
	/**
	 * Retrieve a registered instance
	 * @param  string  $name
	 * @throws Exception\ServiceNotFoundException
	 * @return object|array
	 */
	function get($name);
	/**
	 * Check for a registered instance
	 * @param  string|array  $name
	 * @return bool
	 */
	function has($name);
}

class ServiceManager implements ServiceLocatorInterface {

  // config:
	$abstractFactories = [];	// Closure|AbstractFactoryInterface 'config_key' = 'abstract_factories'
	$factories = [];			// Closure|FactoryInterface|string  'config_key' = 'factories'
	$invokableClasses = [];		// string 							'config_key' = 'invokables'
	$instances = [];			// object Service 					'config_key' = 'services'
	$aliases = [];				// string 							'config_key' = 'aliases'
	$shared = [];				// bool								'config_key' = 'shared'
	$delegators = [];			// string 							'config_key' = 'delegators'
	$initializers = [];			// callable|InitializerInterface 	'config_key' = 'initializers'
	$allowOverride = false;		// bool 							'config_key' = 'allow_override'

  // для внутреннего использования
	$canonicalNames = [];						// Lookup for canonicalized names.
	$pendingAbstractFactoryRequests = [];
	$nestedContextCounter = -1;
	$nestedContext = [];
	$peeringServiceManagers = [];				// ServiceManager
	$shareByDefault = true;						// bool
	$retrieveFromPeeringManagerFirst = false; 	// bool
	$throwExceptionInCreate = true; 			// bool
	$canonicalNamesReplacements = ['-' => '', '_' => '', ' ' => '', '\\' => '', '/' => ''];

  ////////////////////// Методы конфигурированмя массивов ServiceManager (сеттеры)

	// Добавить Closure|AbstractFactoryInterface $factory в $abstractFactories[] = $factory
	addAbstractFactory($factory, $topOfStack = true) return $this

	// Добавить сервис $serviceName как delegator factory в $delegators[$serviceName][] = string $delegatorFactoryName
	addDelegator($serviceName, $delegatorFactoryName) return $this

	// Добавить callable|InitializerInterface инициализатор в $initializers[]	= callable|InitializerInterface $initializer
	addInitializer($initializer, $topOfStack = true) return $this

	// Добавить Closure|AbstractFactoryInterface|string $factory в $factory[$name] = Closure|FactoryInterface $factory;
	setFactory($name, $factory, $shared = null) return $this

	// Добавить string $invokableClass в invokableClasses[$name] = string $invokableClass
	setInvokableClass($name, $invokableClass, $shared = null) return $this

	// Зарегистрировать сервис object $service в $instances[$name] = object $service
	setService($name, $service) return $this

	// Добавить альяс string $nameOrAlias в $aliases[$alias] = string $nameOrAlias
	setAlias($alias, $nameOrAlias) return $this;

	// Зарегистрировать доступ к сервису $name в $shared[$name] = bool $shared
	setShared($name, $isShared) return $this

	// Set allow override - Задать разрешение на переопределение сервиса $this->allowOverride = (bool) $allowOverride
	setAllowOverride($allowOverride) return $this

  ////////////////////// Методы для внешнего использования (геттеры)

	// Проверка наличия запрашиапемого сервиса во всех свойствах-массивах-хранилищах ServiceManager
	has($name, $checkAbstractFactories = true, $usePeeringServiceManagers = true)

	// Retrieve a registered instance $name - ищет во всех свойствах-массивах ServiceManager, создаёт и возвращает объект запрашиваемого сервиса
	get($name, $usePeeringServiceManagers = true) return object $instance

	// Get allow override - получить разрешение на переопределение сервиса
	getAllowOverride() return bool $this->allowOverride

	// Retrieve a keyed list of all registered services. Handy for debugging!
	getRegisteredServices() return array( 'invokableClasses' =>,'factories' =>,'aliases' =>,'instances' =>, );

  ///////////////////// Методы для внутреннего использования

	// ??? Create scoped service manager
	createScopedServiceManager($peering = self::SCOPE_PARENT)
	// ??? Add a peering relationship
	addPeeringServiceManager(ServiceManager $manager, $peering = self::SCOPE_PARENT)

	// Set flag indicating whether services are shared by default
	setShareByDefault($shareByDefault)
	// Are services shared by default?
	shareByDefault() return $this->shareByDefault

	// Set throw exceptions in create
	setThrowExceptionInCreate($throwExceptionInCreate)
	// Get throw exceptions in create
	getThrowExceptionInCreate() return $this->throwExceptionInCreate

	// Set flag indicating whether to pull from peering manager before attempting creation
	setRetrieveFromPeeringManagerFirst($retrieveFromPeeringManagerFirst = true)
	// Should we retrieve from the peering manager prior to attempting to create a service?
	retrieveFromPeeringManagerFirst() return $this->retrieveFromPeeringManagerFirst

	// Resolve the alias for the given canonical name
	resolveAlias($cName) return $cName

	// Ensure the alias definition will not result in a circular reference - используется в setAliases()
	checkForCircularAliasReference($alias, $nameOrAlias) return bool

	// Determine if we have an alias - используется в setAliases(), checkForCircularAliasReference()
	hasAlias($alias) return bool

	// Attempt to retrieve an instance via a peering manager
	retrieveFromPeeringManager($name)

	// Делает из $name Canonicalize name
	canonicalizeName($name) return string $cname
	// Allows to override the canonical names lookup map with predefined values.
	setCanonicalNames($canonicalNames) return $this
	// Retrieve a keyed list of all canonical names. Handy for debugging!
	getCanonicalNames() return array $this->canonicalNames

	// checkNestedContext
	checkNestedContextStart($cName) return $this
	checkNestedContextStop($force = false) return $this

	/* Unregister a service
	* Called when $allowOverride is true and we detect that a service being
	* added to the instance already exists. This will remove the duplicate
	* entry, and also any shared flags previously registered. */
	// используется в setService() и setFactory()
	unregisterService($canonical)

  ///////////////////// Логика внутреннего создания объекта запрашиваемого сервиса

	// Можно ли создать объект из запрашиваемого сервиса
	canCreate($name, $checkAbstractFactories = true) return bool
	// Determine if we can create an instance from an abstract factory
	canCreateFromAbstractFactory($cName, $rName) return bool

	// Создаёт объект запрашиваемого сервиса - используется при запросе объекта сервиса в get()
	create($name) return object $instance
		// Создаёт конкретный объект запрашиваемого сервиса - к doCreate() обращается create(), а к последнему из get()
		doCreate($rName, $cName) return object $instance
		// Create an instance
			createFromInvokable($canonicalName, $requestedName) return object $instance
			// Create an instance via an invokable class
			createFromFactory($canonicalName, $requestedName) return object $instance
			// Create an instance via an abstract factory
			createFromAbstractFactory($canonicalName, $requestedName) return object $instance
				// Create service via callback
				createServiceViaCallback($callable, $cName, $rName) return object $instance

		// Create an instance from DelegatorFactory
		createDelegatorFromFactory($canonicalName, $requestedName) return object $instance
			// Creates a callback that uses a delegator to create a service
			private createDelegatorCallback($delegatorFactory, $rName, $cName, $creationCallback) return object $instance

}