<?php
/*
 * Алгоритм (в обратном порядке) получения(создания) объекта из запрашиваемого сервиса, зарегистрированного в ServiceManager(Plugin Manager)
 * Все методы по созданию объекта сервиса реализованы в ServiceManager (по наследству передаются Plugin Manager)
 * Запуская $this->getServiceLocator()->get(name сервиса) мы запускаем алгоритм создания объекта из запрашиваемого name сервиса
 * Все методы, кроме get(), на практике будут мало используемы, но периодически их надо разбирать для понимания работы ServiceManager
 * Все методы максимально упрощены, чтобы понять логику создания объекта.
 */

/**
 * 1) Получить объект из запрашиваемого сервиса: $this->getServiceLocator()->get(name сервиса)
 * getServiceLocator() возвращает объект класса ServiceManager, внедрённый в свойство объекта $this
 */
function get($name) {
	// запускается создание объекта из запрашиваемого сервиса -> п.2
	$instance = $this->create(array($name));
	return $instance
}

/**
 * 2) Из get($name)-create($name) запускается создание объекта из запрашиваемого сервиса c названием $name:
 */
function create($name) {
	// запускается получение конкретного объекта из запрашиваемого сервиса ->п.3
	return $this->doCreate($rName, $cName);
}

/**
 * 3) Из get($name)-create($name)-doCreate($rName, $cName) запускается получение конкретного объекта из запрашиваемого сервиса ($rName, $cName)
 */
function doCreate($rName, $cName) {
	// В зависимости где зарегитрирован сервис вызывается соответствующий креатор сервиса ->п.4.1-4.3
	if (isset($this->factories[$cName])) {
		$instance = $this->createFromFactory($cName, $rName);
	}
	if ($instance === null && isset($this->invokableClasses[$cName])) {
		$instance = $this->createFromInvokable($cName, $rName);
	}
	if ($instance === null && $this->canCreateFromAbstractFactory($cName, $rName)) {
		$instance = $this->createFromAbstractFactory($cName, $rName);
	}
	return $instance;
}

/**
 * 4.1) Из get($name)-create($name)-doCreate($rName, $cName)-createFromFactory($cName, $rName) запускается креатор создания объекта из фабрики:
 *
 * Все фабрики в ZF2 регистрируют в ServiceManager(Plugin Manager) как сервисы с помощью конфигурации через Config.
 * Надо чтобы фабрики были имплементированы от FactoryInterface и реализовывали его метод createService(), иначе ServiceManager не сможет создать объект сервиса из фабрики
 */
function createFromFactory($canonicalName, $requestedName) {
	$factory = $this->factories[$canonicalName];

	// если сервис зарегистрирован как строковое название соответствующего класса фабрики
	if (is_string($factory)) $factory = new $factory;

	// если сервис представляет собой фабрику от FactoryInterface и он
	if ($factory instanceof FactoryInterface) {
		// передаём в createServiceViaCallback(callable $callable) п.5.1 в качестве $callable метод createService() объекта $factory ($factory->createService())
		$instance = $this->createServiceViaCallback(array($factory, 'createService'), $canonicalName, $requestedName);
	// если сервис зарегистрирован из конфига как Closure (callable-функция)
	} elseif (is_callable($factory)) {
		// передаём в createServiceViaCallback(callable $callable) п.5.1 в качестве $callable объект Closure (callable-функция)
		$instance = $this->createServiceViaCallback($factory, $canonicalName, $requestedName);
	}
	return $instance;
}

/**
 * 4.2) Из get($name)-create($name)-doCreate($rName, $cName)-createFromAbstractFactory($canonicalName, $requestedName) запускается креатор создания объекта из абстрактной фабрики:
 *
 * Абстрактные фвбрики должны быть имплементированы от AbstractFactoryInterface и реализовывать методы
 * canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
 * createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
 * необходимые для создания объекта-сервиса из ServiceManager(PluginManager)
 */
function createFromAbstractFactory($canonicalName, $requestedName) {
	/*
	 * Передаём в createServiceViaCallback(callable $callable) п.5.1 в качестве $callable
	 * метод createServiceWithName() объекта $abstractFactory ($abstractFactory->createServiceWithName())
	 */
	$instance = $this->createServiceViaCallback(array($abstractFactory, 'createServiceWithName'), $canonicalName, $requestedName);

}
		/**
		 * 5.1) Из get($name)-create($name)-doCreate($rName, $cName)-createFromFactory($cName, $rName)-createServiceViaCallback($callable, $cName, $rName).
		 * 5.2) Из get($name)-create($name)-doCreate($rName, $cName)-createFromAbstractFactory($cName, $rName)-createServiceViaCallback($callable, $cName, $rName)
		 * запускается создание объекта сервиса из callable-функции
		 */
		function createServiceViaCallback($callable, $cName, $rName) {
			/*
			 * вызываем $callable-функцию(в нашем случае метод $factory->createService() или $abstractFactory->createServiceWithName())
			 * и передаём в неё параметр $this - объект ServiceManager(Plugin Manager), в котором зарегистрирована фабрика
			 */
			$instance = call_user_func($callable, $this, $cName, $rName);
			return $instance;
		}

/**
 * 4.3) Из get($name)-create($name)-doCreate($rName, $cName)-createFromInvokable($canonicalName, $requestedName)
 * получаем объект сервиса invokable-класса
 */
function createFromInvokable($canonicalName, $requestedName) {
	$invokable = $this->invokableClasses[$canonicalName];
	return new $invokable;
}


/*
 * В случае вызова декорируемого сервиса $this->getServiceLocator()->get(name декорируемого сервиса)
 * получение объекта сервиса идёт по схеме:
 * 	get(name декорируемого сервиса)
 * 		create($name)
 * 			createDelegatorFromFactory( $canonicalName, $requestedName )
 * 				createDelegatorCallback( $delegatorFactory, $rName, $cName, $creationCallback )
 * 					doCreate($rName, $cName)
 * 						createFromAbstractFactory($canonicalName, $requestedName)
 * 							createServiceViaCallback($callable, $cName, $rName)
 * 								return $instance;
 */


