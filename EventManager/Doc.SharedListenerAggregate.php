<?php
/**
 * Чтобы зарегистрировать несколько слушателей для одного события или даже одновременно для нескольких событий,
 * когда еще нет экземпляра класса $id c внедрённым в свойство объектом класса EventManager, используют SharedListenerAggregateInterface.
 * Для этого нужно сделать собственный класс, реализующий интерфейс Zend\EventManager\SharedListenerAggregateInterface
 * и, таким образом, содержащий методы attachShared() и detachShared().
 * Конкретные слушатели регистрируются в теле этих методов.
 */

//use Zend\EventManager\SharedListenerAggregateInterface;
//use Zend\EventManager\SharedEventManagerInterface;

class MySharedListenerAggregate implements SharedListenerAggregateInterface {

	protected $listeners = array();		// listeners['id'] = CallbackHandler $listner

	function attachShared( SharedEventManagerInterface $sharedEventManager ) {
		$this->listeners['Foo'] = array(
				$sharedEventManager->attach( 'Foo', 'getGreeting', function($e){ /* код слушателя1 */ }),
				$sharedEventManager->attach( 'Foo', 'getGreeting', function($e){ /* код слушателя2 */ }),
				$sharedEventManager->attach( 'Foo', 'refreshGreeting', function($e){ })
		);
	}

	function detachShared( SharedEventManagerInterface $sharedEventManager ) {
		foreach ($this->listeners as $id => $listner) {
			if ($sharedEventManager->detach($id, $listner))
				unset($this->listeners[$id]);
		}
	}
}

$sharedEventManager = new Zend\EventManager\SharedEventManager();
$sharedListnerAggregate = new \Helloworld\Event\MySharedListenerAggregate();

// Добавление слушателей $sharedListnerAggregate:
$sharedEventManager->attachAggregate($sharedListnerAggregate);

// Удаление слушателей $sharedListnerAggregate:
$sharedEventManager->detachAggregate($sharedListnerAggregate);

/*
 * Для упрощения написания таких аггрегатов, можно создать свой trait по аналогии с Zend\EventManager\ListenerAggregateTrait
 */
// use Zend\EventManager\SharedEventManagerInterface;
trait SharedListenerAggregateTrait {

	protected $listeners = array();

	function detachShared( SharedEventManagerInterface $sharedEventManager ) {
		foreach ($this->listeners as $id => $listner) {
			if ($sharedEventManager->detach($id, $listner))
				unset($this->listeners[$id]);
		}
	}
}
/*
 * С SharedListenerAggregateTrait код выглядит:
 */

// use Zend\EventManager\SharedListenerAggregateInterface;
// use Zend\EventManager\SharedEventManagerInterface;
class MySharedListenerAggregate implements SharedListenerAggregateInterface {

	use /.../SharedListenerAggregateTrait;

	function attachShared( SharedEventManagerInterface $sharedEventManager ) {
		$this->listeners['Foo'] = array(
				$sharedEventManager->attach( 'Foo', 'getGreeting', function($e){ /* код слушателя1 */ }),
				$sharedEventManager->attach( 'Foo', 'getGreeting', function($e){ /* код слушателя2 */ }),
				$sharedEventManager->attach( 'Foo', 'refreshGreeting', function($e){ })
		);
	}
}

/*
 * Если создавать свой класс от SharedEventManagerInterface и необходимо, чтобы этот класс аггрегировал в сеье объекты от SharedEventManagerInterface,
 * надо имплементировать его от интерфейса SharedEventAggregateAwareInterface и реализовывал его методы-агреггаторы:
 * 		attachAggregate(SharedListenerAggregateInterface $aggregate, $priority = 1)
 * 		detachAggregate(SharedListenerAggregateInterface $aggregate)
 * А лучше свой класс унаследовать сразу от SharedEventManager, который уже реализует аггрегатор SharedEventAggregateAwareInterface (Aware - знает о нём)
 * реализует его методы attachAggregate() и detachAggregate() и поэтому может работать с аггрегаторами слушателей от SharedListenerAggregateInterface
 * как показано выше.
 * SharedEventAggregateAwareInterface - для собственной очень специфичной реализации класса SharedEventManager
 */




///////////////////////////////////// Интерефейс ///////////////////////////////////////////////////

namespace Zend\EventManager;

/**
 * Interface for self-registering event listeners.
 * Classes implementing this interface may be registered by name or instance
 * with a SharedEventManager, without an event name. The {@link attach()} method will
 * then be called with the current SharedEventManager instance, allowing the class to
 * wire up one or more listeners.
 */
interface SharedListenerAggregateInterface {
	/**
	 * Attach one or more listeners
	 * Implementors may add an optional $priority argument; the SharedEventManager
	 * implementation will pass this to the aggregate.
	 * @param SharedEventManagerInterface $events
	 */
	public function attachShared(SharedEventManagerInterface $events);

	/**
	 * Detach all previously attached listeners
	 * @param SharedEventManagerInterface $events
	 */
	public function detachShared(SharedEventManagerInterface $events);
}









