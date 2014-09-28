<?php
/*
 * Если нужно создать слушателя, когда еще нет экземпляра класса $id c внедрённым в свойство объектом класса EventManager,
 * используется SharedEventManager.
 * Тогда событие $event идентифицируют с классом $id, в который будет позже внедрён объект EventManager,
 * и задают слушателя $callback для события с приоритетом выполнения события $priority:
 *
 * 		$id - string|array - имя класса, в который будет внедряться объект EventManager через setEventManager()
 * 		  Если рассматривать пример в Doc.EventManager_Injection.php, то $id - это  __CLASS__ в примере.
 *		$event - string|array - название события (имя метода события)
 *		$callback - callback-функция слушателя
 */
	SharedEventManager::attach($id, $event, $callback, $priority = 1) {
		/*...*/
		$this->identifiers[$id] = new EventManager($id);
		/*...*/
		$this->identifiers[$id]->attach($event, $callback, $priority);
		/*...*/
	}

	$events = new SharedEventManager();

	// Attach to many events on the context "foo"
	$events->attach('foo', array('these', 'are', 'event', 'names'), $callback);
	// Attach to many events on the contexts "foo" and "bar"
	$events->attach(array('foo', 'bar'), array('these', 'are', 'event', 'names'), $callback);

	// Attach to all events on the context "foo"
	$events->attach('foo', '*', $callback);
	// Attach to all events on the contexts "foo" and "bar"
	$events->attach(array('foo', 'bar'), '*', $callback);

/*
 * Объединение экземпляра EventManager с известным SharedEventManager происходит с помощью метода EventManager::setSharedManager(SharedEventManagerInterface $sharedEventManager)
 * Объект EventManager будет запрашивать EventManager::sharedManager в EventManager::trigger($event),
 * прикрепляя слушателей SharedManager к слушателям EventManager для вызванного события $event
 * и исполняя их в порядке приоритета или при равном приоритете сначала слушателей из EventManager::events, а затем из EventManager::sharedManager (порядок уточнить?).
 */

/*
 * Упрощённый пример из мануала
 */

// use Zend\EventManager\EventManagerAwareInterface;
// use Zend\EventManager\EventManagerInterface;
// use Zend\EventManager\EventManager;
class Foo {
 	use Zend\EventManager\EventManagerAwareTrait;

 	function bar($baz, $bat = null) {
 		$params = array('baz', 'bat');
 		$this->getEventManager()->trigger(__FUNCTION__, $this, $params);
 	}
}

// Инициализируем событие 'bar' для класса 'Foo'
$events = new Zend\EventManager\SharedEventManager();
$events->attach('Foo', 'bar', function ($e) use () { });

// Позднее, создаём объект $foo, в который внедряется объект EventManager благодаря use Zend\EventManager\EventManagerAwareTrait
$foo = new Foo();
// Прикрепляем SharedManager-события $events к объекту EventManager, внедрённому в объект $foo
$foo->getEventManager()->setSharedManager($events);

// При вызове метода bar() запустится trigger(__FUNCTION__), который вызовет callback-слушателя function ($e) use () { }
$foo->bar('baz', 'bat');


/////////////////////////////////////////// SharedEventManager //////////////////////////////////////////////////
//namespace Zend\EventManager;
//use Zend\Stdlib\CallbackHandler;
//use Zend\Stdlib\PriorityQueue;
class SharedEventManager implements SharedEventAggregateAwareInterface, SharedEventManagerInterface {
	/**
	 * Массив объектов EventManager($id), где $id - идентификаторы классов, в которые будет внедрён объект EventManager, содержащий этот SharedEventManager
	 * @var array - identifiers[$id] = EventManager($id);
	 */
	protected $identifiers = array();

	/**
	 * Прикрепить слушателей к событию EventManager($id)->attach($event, $callback, $priority)
	 * Не допускается прикреплять объекты SharedListenerAggregateInterface с помощью этого метода. Для этого используется attachAggregate()
	 * Но можно вместо $event, $callback использовать ListenerAggregate (см. Doc.ListenerAggregate.php)
	 * @param  string|array $id - идентификаторы классов, в которые будет внедрён объект EventManager, содержащий этот SharedEventManager
	 * @param  string $event
	 * @param  callable $callback PHP Callback
	 * @param  int $priority Priority at which listener should execute
	 * @return CallbackHandler|array Either CallbackHandler or array of CallbackHandlers
	*/
	function attach( $id, $event, $callback, $priority = 1 ) {
		$ids = (array) $id;
		$listeners = array();
		foreach ($ids as $id) {
			if (!array_key_exists($id, $this->identifiers)) {
				$this->identifiers[$id] = new EventManager($id);
			}
			// EventManager($id)->attach($event, $callback, $priority)
			$listeners[] = $this->identifiers[$id]->attach($event, $callback, $priority);
		}
		if (count($listeners) > 1) {
			return $listeners;
		}
		return $listeners[0];
	}

	/**
	 * Прикрепить listener aggregate. (См. Doc.SharedListnerAggregate.php)
	 * @param  SharedListenerAggregateInterface $aggregate
	 * @param  int $priority If provided, a suggested priority for the aggregate to use
	 * @return mixed return value of {@link ListenerAggregateInterface::attachShared()}
	 */
	function attachAggregate( SharedListenerAggregateInterface $aggregate, $priority = 1 ) {
		return $aggregate->attachShared($this, $priority);
	}

	/**
	 * Удалить слушателей CallbackHandler $listener из EventManager($id) ()
	 * @param  string|int $id
	 * @param  CallbackHandler $listener
	 * @return bool Returns true if event and listener found, and unsubscribed; returns false if either event or listener not found
	 */
	function detach( $id, CallbackHandler $listener ) {
		if (!array_key_exists($id, $this->identifiers)) {
			return false;
		}
		// EventManager($id)->detach($listener)
		return $this->identifiers[$id]->detach($listener);
	}

	/**
	 * Удалить listener aggregate (См. Doc.SharedListnerAggregate.php)
	 * @param  SharedListenerAggregateInterface $aggregate
	 * @return mixed return value of {@link SharedListenerAggregateInterface::detachShared()}
	 */
	function detachAggregate( SharedListenerAggregateInterface $aggregate ) {
		return $aggregate->detachShared($this);
	}

	/**
	 * Получить массив имён всех событий для данного идентификатора: $this->identifiers[$id]->getEvents()
	 * @param  string|int $id
	 * @return array
	 */
	function getEvents( $id ) {
		if (!array_key_exists($id, $this->identifiers)) {
			//Check if there are any id wildcards listeners
			if ('*' != $id && array_key_exists('*', $this->identifiers)) {
				return $this->identifiers['*']->getEvents();
			}
			return false;
		}
		// $this->EventManager($id)->getEvents()
		return $this->identifiers[$id]->getEvents();
	}

	/**
	 * Вернуть всех слушателей для данного события и индификатора: $this->identifiers[$id]->getListeners($event)
	 * @param  string|int $id
	 * @param  string|int $event
	 * @return false|PriorityQueue
	 */
	function getListeners( $id, $event ) {
		if (!array_key_exists($id, $this->identifiers)) {
			return false;
		}
		// $this->EventManager($id)->getListeners($event)
		return $this->identifiers[$id]->getListeners($event);
	}

	/**
	 * Удалить всех слушателей для данного идентификатора и события (опционально)
	 * @param  string|int $id
	 * @param  null|string $event
	 * @return bool
	 */
	function clearListeners( $id, $event = null ) {
		if (!array_key_exists($id, $this->identifiers)) {
			return false;
		}
		if (null === $event) {
			unset($this->identifiers[$id]);
			return true;
		}
		// $this->EventManager($id)->clearListeners($event)
		return $this->identifiers[$id]->clearListeners($event);
	}
}

