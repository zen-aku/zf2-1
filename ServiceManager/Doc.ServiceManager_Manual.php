<?php
/*
 * ServiceManager обеспечивает следующее API.
 */
$serviceManager = new Zend\ServiceManager\ServiceManager();

/**
 * 1. Service registration.
 * ServiceManager::setService() - позволяет регистрировать объект как сервис
 */
$serviceManager->setService('my-foo', new stdClass());
$serviceManager->setService('my-settings', array('password' => 'super-secret'));

var_dump($serviceManager->get('my-foo')); 		// вызывает объект new stdClass()
var_dump($serviceManager->get('my-settings')); 	// array('password' => 'super-secret')

/**
 * 2. Lazy-loaded service objects.
 * ServiceManager::setInvokableClass() - позволяет указать какой класс надо использовать при запросе сервиса
 */
$serviceManager->setInvokableClass('foo-service-name', 'Fully\Qualified\Classname');
var_dump($serviceManager->get('foo-service-name')); // создаёт объект из класса Fully\Qualified\Classname

/**
 * 3. Service factories.
 * Вместо указания реального экземпляра класса или имени класса есть возможность вызвать соответствующую фабрику,
 * которая создаст нужный экземпляр объекта.
 * Фабрикой может быть:
 * 		- любая callback-функция или
 * 		- объект, реализующий интерфейс Zend\ServiceManager\FactoryInterface
 *      - имя класса, реализующего интерфейс Zend\ServiceManager\FactoryInterface
 */
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MyFactory implements FactoryInterface {
	function createService( ServiceLocatorInterface $serviceLocator ) {
		return new \stdClass();
	}
}

// регистрируем объект фабрики как сервис-factory
$serviceManager->setFactory('foo-service-name', new MyFactory());
// регистрируем имя класса-фабрики как сервис-factory
$serviceManager->setFactory('bar-service-name', 'MyFactory');
// регистрируем callback(объект Closure) как сервис-factory
$serviceManager->setFactory('baz-service-name', function () { return new \stdClass(); });

var_dump($serviceManager->get('foo-service-name')); 	// stdClass(1)
var_dump($serviceManager->get('bar-service-name')); 	// stdClass(2)
var_dump($serviceManager->get('baz-service-name')); 	// stdClass(3)

/**
 * 4. Service aliasing.
 * C помощью ServiceManager::setAlias() можно задать псевдоним любому зарегистрированному сервису, фабрике
 * или invokable, или даже другому псевдониму.
 */
$foo = new \stdClass();
$foo->bar = 'baz!';

$serviceManager->setService('my-foo', $foo);
$serviceManager->setAlias('my-bar', 'my-foo');	// задаём псевдоним 'my-bar' сервису 'my-foo'
$serviceManager->setAlias('my-baz', 'my-bar');	// задаём псевдоним 'my-baz' псевдониму 'my-bar'

var_dump($serviceManager->get('my-foo')->bar); // baz!
var_dump($serviceManager->get('my-bar')->bar); // baz!
var_dump($serviceManager->get('my-baz')->bar); // baz!

/**
 * 5. Abstract factories.
 * Абстрактная фабрика должна реализовывать Zend\ServiceManager\AbstractFactoryInterface
 * Абстрактная фабрика может рассматриваться как «резервная» фабрика:
 * 	  если ServiceManager не может найти сервис с запрошенным именем,
 * 	  то будут проверяться зарегистрированные абстрактные фабрики, пока не сможет вернуться объект
 * Абстрактная фабрика - это класс, содержащий универсальный! фабричный метод createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName),
 * который возвращает объект не какого-то заранее зарегистрированного одного сервиса - соответствующего обычной фабрике,
 * а возвращает объекты разных сервисов $requestedName, которые создаются из этой абстрактной фабрики
 * ???Его надо использовать для сооздания множества однотипных сервисов, которые желательно регистрировать в хранилище сервисов как одну единую группу (а не множество отдельных сервисов)
 * и вызывать сервисы из Abstract factory по ключам - именам классов-сервисов.
 * Фактически Абстрактная фабрика реализует паттерн FlyweightFactory
 */
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\AbstractFactoryInterface;

class MyAbstractFactory implements AbstractFactoryInterface {

	function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName) {
        // this abstract factory only knows about 'foo' and 'bar'
        return $requestedName === 'foo' || $requestedName === 'bar';
    }

    function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName) {
        $service = new \stdClass();
        $service->name = $requestedName;
        return $service;
    }
}

$serviceManager->addAbstractFactory('MyAbstractFactory');

var_dump($serviceManager->get('foo')->name); 	// foo
var_dump($serviceManager->get('bar')->name); 	// bar
var_dump($serviceManager->get('baz')->name); 	// exception! Zend\ServiceManager\Exception\ServiceNotFoundException

/**
 * 6. Initializers.
 * Initializers - это callback или имена классов, реализующих интерфейс Zend\ServiceManager\InitializerInterface
 * и автоматически инициализирующие сервисы во время их вызова командой $serviceManager->get('name service')
 * Они получают объект сервиса и манипулируют им (инициализируют его).
 * В Initializers можно добавить несколько инициализаторов и они все будут запущены по команде ->initialized в поряд очереди добавления
 */
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\InitializerInterface;

class MyInitializer implements InitializerInterface {

	function initialize ($instance, ServiceLocatorInterface $serviceLocator) {
		if ($instance instanceof \stdClass) {
			$instance->initialized = 'initialized!';
		}
	}
}

$serviceManager->addInitializer('MyInitializer');
//$serviceManager->addInitializer('MyInitializer2');

// добавим Invokable - сервис
$serviceManager->setInvokableClass('my-service', 'stdClass');

// проинициализируем сервис 'my-service'
var_dump($serviceManager->get('my-service')->initialized); // initialized!


