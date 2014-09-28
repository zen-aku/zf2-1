<?php
/**
 * Чтобы зарегистрировать несколько слушателей для одного события или даже одновременно для нескольких событий
 * используют ListenerAggregateInterface. Для этого нужно сделать собственный класс,
 * реализующий интерфейс Zend\EventManager\ListenerAggregateInterface и, таким образом, содержащий методы attach() и detach().
 * Конкретные слушатели регистрируются в теле этих методов.
 */

//use Zend\EventManager\ListenerAggregateInterface;
//use Zend\EventManager\EventManagerInterface;

class MyGetGreetingEventListenerAggregate implements ListenerAggregateInterface {

	protected $listeners = array();

	function attach(EventManagerInterface $eventManager) {
		$this->listeners = array(
			$eventManager->attach( 'getGreeting', function($e){ /* код слушателя1 */ }),
			$eventManager->attach( 'getGreeting', function($e){ /* код слушателя2 */ }),
			$eventManager->attach( 'refreshGreeting', function($e){ })
		);
	}
	// этот мнтод реализован в ListenerAggregateTrait
	function detach(EventManagerInterface $events) {
		/* код реализующий удаление всех слушателей, зарегистрированных с помощью MyGetGreetingEventListenerAggregate */
	}
}
// Добавление слушателей:
$greetingService->getEventManager()->attach( new \Helloworld\Event\MyGetGreetingEventListenerAggregate() );

/*
 * С ListenerAggregateTrait код выглядит:
 */
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateTrait;

class MyGetGreetingEventListenerAggregate implements ListenerAggregateInterface {

	use ListenerAggregateTrait;

	function attach(EventManagerInterface $eventManager) {
		$this->listeners = array(
				$eventManager->attach( 'getGreeting', function($e){ /* код слушателя1 */ }),
				$eventManager->attach( 'getGreeting', function($e){ /* код слушателя2 */ }),
				$eventManager->attach( 'refreshGreeting', function($e){ })
		);
	}
}
// Добавление слушателей:
$greetingService->getEventManager()->attach( new \Helloworld\Event\MyGetGreetingEventListenerAggregate() );

///////////////////////////////////////// Интерфейсы //////////////////////////////////////////////////////
//namespace Zend\EventManager;
/**
 * Interface for self-registering event listeners.
 * Classes implementing this interface may be registered by name or instance
 * with an EventManager, without an event name. The {@link attach()} method will
 * then be called with the current EventManager instance, allowing the class to
 * wire up one or more listeners.
 */
interface ListenerAggregateInterface {
	/**
	 * Attach one or more listeners
	 * Implementors may add an optional $priority argument; the EventManager implementation will pass this to the aggregate.
	 */
	function attach(EventManagerInterface $events);

	/**
	 * Detach all previously attached listeners
	 */
	function detach(EventManagerInterface $events);
}

/**
 * Provides logic to easily create aggregate listeners, without worrying about
 * manually detaching events
 */
trait ListenerAggregateTrait {
	/**
	 * @var \Zend\Stdlib\CallbackHandler[]
	 */
	protected $listeners = array();
	/**
	 * Detach all previously attached listeners
	 */
	function detach(EventManagerInterface $events) {
		foreach ($this->listeners as $index => $callback) {
			if ($events->detach($callback)) {
				unset($this->listeners[$index]);
			}
		}
	}
}










