<?php
/*
 *  Шаблон Delegation (близнец Decorator) передаёт(делегирует) поведение(функционал)  объекта одного класса-декорируемого
 *  в объект другого класса-декоратора. И теперь из объекта-декоратора будет доступен функционал декорируемого класса.
 *  Этот паттерн предназначен для модификации или замены фуункционала делегируемого класса, но при этом не меняя сам класс.
 *
 *  К применению в ZF2:
 *  	- когда необходимо изменить какой-то сервис, не изменяя его код, тогда используют ServiceManager c DelegatorFactoryInterface
 *	Примеры из мануала:
 */

//use Zend\EventManager\EventManagerInterface;
//use Zend\ServiceManager\DelegatorFactoryInterface;
//use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Оригинальный класс-сервис(декорируемый) с методом buzz(), который необходимо модифицмровать (декорировать)
 */
class Buzzer {
	function buzz() {
		return 'Buzz!';
	}
}
/**
 * Класс-декоратор, который модифицирует метод buzz() класса Buzzer,
 */
class BuzzerDelegator extends Buzzer {
	protected $realBuzzer;
	protected $eventManager;

	function __construct(Buzzer $realBuzzer, EventManagerInterface $eventManager) {
		$this->realBuzzer   = $realBuzzer;
		$this->eventManager = $eventManager;
	}
	// декорируем метод buzz() класса Buzzer
	function buzz(){
		$this->eventManager->trigger('buzz', $this);
		return $this->realBuzzer->buzz();
	}
}

/**
 * Фабрика сервиса-декоратора, которая использует класс BuzzerDelegator в качестве декоратора
 * Она должна быть имплементирована от DelegatorFactoryInterface и реализовывть его метод createDelegatorWithName(),
 * который вызывается в ServiceManager при запросе get('декорируемый сервис')
 */
class BuzzerDelegatorFactory implements DelegatorFactoryInterface {
	/**
	 * Этот метод будет вызван в ServiceManager при запросе get('декорируемый сервис'), в котором зарегистрирована эта фабрика,
	 * передаст самого себя и вернёт !!!объект декоратора BuzzerDelegator с внедрёнными Buzzer $realBuzzer и $eventManager с 'декором'
	 * $callback - Closure, которая при invoke-вызове возвращает объект деаорируемого сервиса $name, $requestedName (в нашем примере это объект класса Buzzer)
	 * @return BuzzerDelegator
	 */
	function createDelegatorWithName( ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback ) {
		$realBuzzer   = call_user_func($callback);
		$eventManager = $serviceLocator->get('EventManager');
		$eventManager->attach('buzz', function () { echo "Stare at the art!\n"; });
		return new BuzzerDelegator($realBuzzer, $eventManager);
	}
}

$serviceManager = new Zend\ServiceManager\ServiceManager();

// добавляем в ServiceManager обычный вызываемый сервис-класс 'buzzer'=> Buzzer
$serviceManager->setInvokableClass('buzzer', 'Buzzer');

// в отличие от обычных фабрик сервисов, фабрика делегатора должна быть зарегистрирована как обычный сервис
// добавляем в ServiceManager декоратор как обычный вызываемый сервис-класс 'buzzer-delegator-factory' => 'BuzzerDelegatorFactory'
$serviceManager->setInvokableClass('buzzer-delegator-factory', 'BuzzerDelegatorFactory');

// говорим сервис-манагеру использовать декоратор (delegator factory) 'buzzer-delegator-factory' при вызове сервиса 'buzzer'
$serviceManager->addDelegator('buzzer', 'buzzer-delegator-factory');

// теперь, если будет вызван сервис 'buzzer', то будет вызван его декоратор delegator factory BuzzerDelegatorFactory
$buzzer = $serviceManager->get('buzzer');
$buzzer->buzz(); // "Stare at the art!\nBuzz!"

/* Можно добавлять несколько декораторов для одного сервиса
 * $serviceManager->addDelegator('buzzer', 'buzzer-delegator-factory1')
 *				  ->addDelegator('buzzer', 'buzzer-delegator-factory2'); */

/**
 * Конфигурирование с помощью config.module.php
 */
$config = array(
		'invokables' => array(
				'buzzer'                   => 'Buzzer',
				'buzzer-delegator-factory' => 'BuzzerDelegatorFactory',
		),
		'delegators' => array(
				'buzzer' => array(
						'buzzer-delegator-factory',
						// можно добавить ещё декораторы для сервиса
				),
		),
);
$serviceManager = new Zend\ServiceManager\ServiceManager();
$buzzer = $serviceManager->get('buzzer');
$buzzer->buzz(); // "Stare at the art!\nBuzz!"


//namespace Zend\ServiceManager;
/**
 * Interface for factories that can create delegates for services
 */
interface DelegatorFactoryInterface {
	/**
	 * A factory that creates delegates of a given service
	 * @param ServiceLocatorInterface $serviceLocator the service locator which requested the service
	 * @param string                  $name           the normalized service name
	 * @param string                  $requestedName  the requested service name
	 * @param callable                $callback       the callback that is responsible for creating the service
	 * @return mixed
	 */
	public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback);
}








