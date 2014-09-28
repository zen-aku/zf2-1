<?php
/*
 * Zend\EventManager дает возможность администрировать слушателей и позволяет классу стать инициатором события
 *
 * Краткий список свщйств и публичных методов EventManager:
 *
 * 	protected $events array 		- PriorityQueue-очередь событий events[string $event][int] => CallbackHandler $listner
 *  protected $eventClass = 'Zend\EventManager\Event'
 *  protected $identifiers array 	- Идентификаторы классов (имя), в которые внедяется объект EventManager
 *  protected $sharedManager SharedEventManagerInterface - SharedEventManager - хранилище событий-слушателей, когда еще нет экземпляра класса $id c внедрённым в свойство объектом класса EventManager (нет привязки события к классу)
 *
 * 	__construct($identifiers = null)	- Конструктор с возможностью задания идентификаторов классов (имён), в которые внедяется объект EventManager
 *  setEventClass($class) 		- Задать свой класс события вместо дефолтного 'Zend\EventManager\Event'
 *  setSharedManager(SharedEventManagerInterface $sharedEventManager) - Задать SharedEventManager в $this->sharedManager для последующего объединения событий-слушателей из SharedEventManager с событиями-слушателями в EventManager
 *  unsetSharedManager() 		- Удалить текущий SharedEventManager из $this->sharedManager
 *  getSharedManager() 			- Задать SharedEventManager в $this->sharedManager для последующего объединения событий-слушателей из SharedEventManager с событиями-слушателями в EventManager
 *  getIdentifiers() 			- Получить все идентификаторы классов (массив имён), в которые внедяется этот объект EventManager
 *  setIdentifiers($identifiers)    - Задать(переопределить) в $this->identifiers неповторяющиеся идентификаторы классов, в которые внедряется этот объект EventManager.
 *	addIdentifiers($identifiers) 	- Добавить в $this->identifiers несуществующие и неповторяющиеся идентификаторы классов, в которые внедряется этот объект EventManager.
 *	trigger($event, $target = null, $argv = array(), $callback = null) - Триггер(запуск) всех слушателей для события $event
 *	triggerUntil($event, $target, $argv = null, $callback = null) - Триггер(запуск) всех слушателей для события $event
 *	attach($event, $callback = null, $priority = 1) - Прикрепить слушателя $callback с приоритетом $priority к событиям $event
 *	attachAggregate(ListenerAggregateInterface $aggregate, $priority = 1) - Прикрепить слушателей из объекта ListenerAggregateInterface $aggregate к событию
 *	detach($listener) 			- Удалить слушателя CallbackHandler $listener из PriorityQueue $this->events[$event][] => CallbackHandler $listener
 *	detachAggregate(ListenerAggregateInterface $aggregate) - Удалить всех слушателей, прикреплённых с помощью ListenerAggregateInterface
 *	getEvents() 				- Вернуть имена всех событий из массива $this->events
 *	getListeners($event) 		- Вернуть всех слушателей для события $event из $this->events[$event]
 *	clearListeners($event) 		- Удалить всех слушателей для события $event
 *	prepareArgs(array $args) 	- Подготавливает аргументы: преобразует массив аргументов $args в ArrayObject($args)
 */

class EventManager implements EventManagerInterface {
	/**
	 * PriorityQueue-очередь событий events[string $event][int] => CallbackHandler $listner
	 * @var array Array of PriorityQueue objects
	 */
	protected $events = array();

	/**
	 * @var string Class representing the event being emitted
	 */
	protected $eventClass = 'Zend\EventManager\Event';

	/**
	 * Идентификаторы классов (имя), в которые внедяется объект EventManager
	 * При внедрении объектов от SharedEventManagerInterface в EventManager, их идентификаторы классов помещаются сюда
	 * @var array
	 */
	protected $identifiers = array();

	/**
	 * SharedEventManager - хранилище событий-слушателей, когда еще нет экземпляра класса $id c внедрённым в свойство объектом класса EventManager (нет привязки события к классу)
	 * @var false|null|SharedEventManagerInterface
	 */
	protected $sharedManager = null;

	/**
	 * Конструктор с возможностью задания идентификаторов классов (имён), в которые внедряется объект EventManager
	 * SharedEventManagerInterface.
	 * @param  null|string|int|array|Traversable $identifiers
	 */
	function __construct($identifiers = null) {
		$this->setIdentifiers($identifiers);
	}

	/**
	 * Задать свой класс события вместо дефолтного 'Zend\EventManager\Event'
	 * @param  string $class
	 * @return $this
	 */
	function setEventClass($class) {
		$this->eventClass = $class;
		return $this;
	}

	/**
	 * Задать SharedEventManager в $this->sharedManager для последующего объединения событий-слушателей из SharedEventManager с событиями-слушателями в EventManager
	 * @param SharedEventManagerInterface $sharedEventManager
	 * @return $this
	 */
	function setSharedManager(SharedEventManagerInterface $sharedEventManager) {
		$this->sharedManager = $sharedEventManager;
		StaticEventManager::setInstance($sharedEventManager);
		return $this;
	}

	/**
	 * Удалить текущий SharedEventManager из $this->sharedManager
	 */
	function unsetSharedManager() {
		$this->sharedManager = false;
	}

	/**
	 * Получить текущий SharedEventManager из $this->sharedManager или из StaticEventManager::getInstance()
	 * Если он не определён в $this->sharedManager, но имеется статический объект SharedEventManager в StaticEventManager,
	 * то записать в $this->sharedManager объект SharedEventManager из StaticEventManager::getInstance() и вернуть его.
	 * Если ничего не найдено, вернуть false
	 * @return false|SharedEventManagerInterface
	 */
	function getSharedManager() {
		// "false" means "I do not want a shared manager; don't try and fetch one"
		if (false === $this->sharedManager || $this->sharedManager instanceof SharedEventManagerInterface ) {
			return $this->sharedManager;
		}
		if (!StaticEventManager::hasInstance()) {
			return false;
		}
		$this->sharedManager = StaticEventManager::getInstance();
		return $this->sharedManager;
	}

	/**
	 * Получить все идентификаторы классов (массив имён), в которые внедяется этот объект EventManager
	 * @return array
	 */
	function getIdentifiers() {
		return $this->identifiers;
	}

	/**
	 * Задать(переопределить) в $this->identifiers неповторяющиеся идентификаторы классов, в которые внедряется этот объект EventManager.
	 * @param string|int|array|Traversable $identifiers
	 * @return $this
	 */
	function setIdentifiers($identifiers) {
		if (is_array($identifiers) || $identifiers instanceof Traversable) {
			$this->identifiers = array_unique((array) $identifiers);
		} elseif ($identifiers !== null) {
			$this->identifiers = array($identifiers);
		}
		return $this;
	}

	/**
	 * Добавить в $this->identifiers несуществующие и неповторяющиеся идентификаторы классов, в которые внедряется этот объект EventManager.
	 * @param string|int|array|Traversable $identifiers
	 * @return $this
	 */
	function addIdentifiers($identifiers) {
		if (is_array($identifiers) || $identifiers instanceof Traversable) {
			$this->identifiers = array_unique(array_merge($this->identifiers, (array) $identifiers));
		} elseif ($identifiers !== null) {
			$this->identifiers = array_unique(array_merge($this->identifiers, array($identifiers)));
		}
		return $this;
	}

	/**
	 * Триггер(запуск) всех слушателей для события $event
	 * trigger() подготавливает данные для последующей их обработки в $this->triggerListeners():
	 * 	- если передан объект события EventInterface в $event, то извлекается имя события в $e,
	 *  	при этом аргументы $target и $argv не задаются, потому что они уже включены в объект $event
	 *  	и тогда вторым параметром ($target) задаётся $callback
	 *  - если передано имя события, то создаётся объект события EventInterface в $event и извлекается имя события в $e
	 *
	 * Эмулирует triggerUntil(), если задан последний параметр callback, в который будет передаваться результат выполнения последнего callback-слушателя и если возвращается TRUE, то слушание прерывается.
	 *
	 * @param  string|EventInterface $event
	 * @param  string|object $target Object calling emit, or symbol describing target (such as static method name)
	 * @param  array|ArrayAccess $argv массив параметров, включается в свойство создаваемого в trigger() объект Event и передаётся вместе с объектом Event в callback-слушателей
	 * @param  null|callable $callback - обрабатывает передаваемый в неё результат выполнения последнего callback-слушателя
	 * @return ResponseCollection All listener return values
	 */
	function trigger($event, $target = null, $argv = array(), $callback = null) {
		if ($event instanceof EventInterface) {
			$e        = $event;
			$event    = $e->getName();
			$callback = $target;
		} elseif ($target instanceof EventInterface) {
			$e = $target;
			$e->setName($event);
			$callback = $argv;
		} elseif ($argv instanceof EventInterface) {
			$e = $argv;
			$e->setName($event);
			$e->setTarget($target);
		} else {
			$e = new $this->eventClass();
			$e->setName($event);
			$e->setTarget($target);
			$e->setParams($argv);
		}

		if ($callback && !is_callable($callback)) {
			throw new Exception\InvalidCallbackException('Invalid callback provided');
		}
		// Initial value of stop propagation flag should be false
		$e->stopPropagation(false);

		return $this->triggerListeners($event, $e, $callback);
	}

	/**
	 * Метод triggerUntil() - полный аналог метода trigger(), но с обязательным вторым параметром $target.
	 * Предполагалось, что при наличии 4-го параметра $callback  возвращаемые значения от слушателей будут попадать в $callback, и если возвращается TRUE, то слушание прерывается.
	 * Но эту же возможность имеет и trigger(), поэтому нет особого смысла применять triggerUntil() вместо trigger(),
	 * разве только для того, чтобы названием вызова (triggerUntil) обозначить, что результат слушателя будет проверяться в $callback.
	 *
	 * @param  string $event
	 * @param  string|object $target Object calling emit, or symbol describing target (such as static method name)
	 * @param  array|ArrayAccess $argv Array of arguments; typically, should be associative
	 * @param  callable $callback
	 * @return ResponseCollection
	 */
	function triggerUntil($event, $target, $argv = null, $callback = null) {
		if ($event instanceof EventInterface) {
			$e        = $event;
			$event    = $e->getName();
			$callback = $target;
		} elseif ($target instanceof EventInterface) {
			$e = $target;
			$e->setName($event);
			$callback = $argv;
		} elseif ($argv instanceof EventInterface) {
			$e = $argv;
			$e->setName($event);
			$e->setTarget($target);
		} else {
			$e = new $this->eventClass();
			$e->setName($event);
			$e->setTarget($target);
			$e->setParams($argv);
		}
		if (!is_callable($callback)) {
			throw new Exception\InvalidCallbackException('Invalid callback provided');
		}
		// Initial value of stop propagation flag should be false
		$e->stopPropagation(false);
		return $this->triggerListeners($event, $e, $callback);
	}


	/**
	 * triggerListeners() (внутренняя функция EventManager) - Завершает процесс запуска слушателей для события, начатый методами trigger() или triggerUntil()
	 * Слушателей из $this->sharedManager прикрепляют к событиям в массиве $this->events[$event] для события $event и выполняют их всех в цикле
	 * Результаты выполнения callback-слушателей помещает в стек ResponseCollection,
	 * проверяя во время перебора слушателей propagationIsStopped для прерывания выполнения слушателей
	 *
	 * @param  string           $event Event name
	 * @param  EventInterface $e
	 * @param  null|callable    $callback
	 * @return ResponseCollection
	 */
	protected function triggerListeners($event, EventInterface $e, $callback = null) {
		$responses = new ResponseCollection;
		$listeners = $this->getListeners($event);	// $listeners = $this->events[$event]

		// Add shared/wildcard listeners to the list of listeners, but don't modify the listeners object
		$sharedListeners         = $this->getSharedListeners($event);
		$sharedWildcardListeners = $this->getSharedListeners('*');
		$wildcardListeners       = $this->getListeners('*');

		if (count($sharedListeners) || count($sharedWildcardListeners) || count($wildcardListeners)) {
			$listeners = clone $listeners;
			// Shared listeners on this specific event
			$this->insertListeners($listeners, $sharedListeners);
			// Shared wildcard listeners
			$this->insertListeners($listeners, $sharedWildcardListeners);
			// Add wildcard listeners
			$this->insertListeners($listeners, $wildcardListeners);
		}
		foreach ($listeners as $listener) {
			$listenerCallback = $listener->getCallback();

			// Trigger the listener's callback, and push its result onto the response collection
			$responses->push(call_user_func($listenerCallback, $e));

			// If the event was asked to stop propagating, do so
			if ($e->propagationIsStopped()) {
				$responses->setStopped(true);
				break;
			}
			// If the result causes our validation callback to return true, stop propagation
			if ($callback && call_user_func($callback, $responses->last())) {
				$responses->setStopped(true);
				break;
			}
		}
		return $responses;
	}

	/**
	 * Прикрепить слушателя $callback с приоритетом $priority к событиям $event
	 * Слушатели добавляются в PriorityQueue $this->events[$event][] => CallbackHandler $listener
	 * Если $event - объект ListenerAggregateInterface, то $callback - это приоритете ($callback-слушатели аггрегированы в объекте ListenerAggregateInterface )
	 * Можно задать "*" для имени события. В таком случае слушатель будет прикреплён ко всем событиям
	 * Метод возвращает объект CallbackHandler $listener, в котором содержится $callback-слушатель.
	 *
	 * @param  string|array|ListenerAggregateInterface 	прикрепляется в PriorityQueue $this->events[$event][] => CallbackHandler $listener
	 * @param  callable|int $callback 	если $event - ListenerAggregateInterface, то $callback - int приортитет
	 * @param  int $priority 			задаёт последовательность выполнения слушателя
	 * @return CallbackHandler|mixed 	CallbackHandler если задан callback-слушатель и mixed если ListenerAggregateInterface
	 */
	function attach($event, $callback = null, $priority = 1) {
		// Proxy ListenerAggregateInterface arguments to attachAggregate()
		if ($event instanceof ListenerAggregateInterface) {
			return $this->attachAggregate($event, $callback);
		}
		// Null callback is invalid
		if (null === $callback) {
			throw new Exception\InvalidArgumentException(sprintf(
					'%s: expects a callback; none provided',
					__METHOD__
			));
		}
		// Array of events should be registered individually, and return an array of all listeners
		if (is_array($event)) {
			$listeners = array();
			foreach ($event as $name) {
				$listeners[] = $this->attach($name, $callback, $priority);
			}
			return $listeners;
		}
		// If we don't have a priority queue for the event yet, create one
		if (empty($this->events[$event])) {
			$this->events[$event] = new PriorityQueue();
		}
		// Create a callback handler, setting the event and priority in its metadata
		$listener = new CallbackHandler($callback, array('event' => $event, 'priority' => $priority));

		// Inject the callback handler into the queue
		$this->events[$event]->insert($listener, $priority);
		return $listener;
	}

	/**
	 * Вызывается из метода EventManager::attach($event) при передаче в качестве $event объекта ListenerAggregateInterface $aggregate
	 * Прикрепить слушателей из объекта ListenerAggregateInterface $aggregate к событию
	 * Объект ListenerAggregateInterface вызывает свой метод attach($this), куда передаётся этот EventManager.
	 * Методе attach() реализует добавление нескольких слушателей для одного или даже нескольких событий
	 * @param  ListenerAggregateInterface $aggregate
	 * @param  int $priority If provided, a suggested priority for the aggregate to use
	 * @return mixed return value of ListenerAggregateInterface::attach()
	 */
	function attachAggregate(ListenerAggregateInterface $aggregate, $priority = 1) {
		return $aggregate->attach($this, $priority);
	}

	/**
	 * Удалить слушателя CallbackHandler $listener из PriorityQueue $this->events[$event][] => CallbackHandler $listener
	 * Как правило, CallbackHandler $listener получают при прикреплении из attach(), сохраняют в переменной чтобы позже его открепить с помощью detach()
	 * Для удаления всех слушателей события $event используют метод EventManager::clearListeners($event)
	 * @param CallbackHandler|ListenerAggregateInterface $listener
	 * @return bool  true если событие и слушательнайдены и удалены, false если или слушательне найден, или событие
	 */
	function detach($listener){
		if ($listener instanceof ListenerAggregateInterface) {
			return $this->detachAggregate($listener);
		}
		if (!$listener instanceof CallbackHandler) {
			throw new Exception\InvalidArgumentException(sprintf(
					'%s: expected a ListenerAggregateInterface or CallbackHandler; received "%s"',
					__METHOD__,
					(is_object($listener) ? get_class($listener) : gettype($listener))
			));
		}
		$event = $listener->getMetadatum('event');
		if (!$event || empty($this->events[$event])) {
			return false;
		}
		$return = $this->events[$event]->remove($listener);
		if (!$return) {
			return false;
		}
		if (!count($this->events[$event])) {
			unset($this->events[$event]);
		}
		return true;
	}

	/**
	 * Вызывается из метода EventManager::detach($event) при передаче в качестве $event объекта ListenerAggregateInterface $aggregate
	 * Удалить всех слушателей, прикреплённых с помощью ListenerAggregateInterface
	 * Объект ListenerAggregateInterface вызывает свой метод detach($this), куда передаётся этот EventManager.
	 * Метод detach() удаляет в цикле всех слушателей объекта ListenerAggregateInterface
	 * @param  ListenerAggregateInterface $aggregate
	 * @return mixed return value of ListenerAggregateInterface::detach()
	 */
	function detachAggregate(ListenerAggregateInterface $aggregate) {
		return $aggregate->detach($this);
	}

	/**
	 * Вернуть имена всех событий из массива $this->events
	 * @return array
	 */
	function getEvents() {
		return array_keys($this->events);
	}

	/**
	 * Вернуть всех слушателей для события $event из $this->events[$event]
	 * @param  string $event
	 * @return PriorityQueue
	 */
	function getListeners($event) {
		if (!array_key_exists($event, $this->events)) {
			return new PriorityQueue();
		}
		return $this->events[$event];
	}

	/**
	 * Удалить всех слушателей для события $event
	 * @param  string $event
	 */
	function clearListeners($event) {
		if (!empty($this->events[$event])) {
			unset($this->events[$event]);
		}
	}

	/**
	 * Подготавливает аргументы: преобразует массив аргументов $args в ArrayObject($args)
	 * для последующей передачи в качестве параметра аргументов $args в trigger($events, $target, $args) или triggerUntil($events, $target, $args)
	 * аргументов $args будут переданы в объект Event, который в свою очередь будет передан в качестве аргумента callback-слушателя
	 * @param  array $args
	 * @return ArrayObject
	 */
	function prepareArgs(array $args) {
		return new ArrayObject($args);
	}

	/**
	 * Внутренний метод.
	 * Получить массив CallbackHandler слушателей из $this->sharedManager для события $event
	 * и класса $id, в который внедрён этот EventManager
	 * @param  string $event
	 * @return array
	 */
	protected function getSharedListeners($event) {
		if (!$sharedManager = $this->getSharedManager()) {
			return array();
		}
		$identifiers     = $this->getIdentifiers();
		//Add wildcard id to the search, if not already added
		if (!in_array('*', $identifiers)) {
			$identifiers[] = '*';
		}
		$sharedListeners = array();

		foreach ($identifiers as $id) {
			if (!$listeners = $sharedManager->getListeners($id, $event)) {
				continue;
			}
			if (!is_array($listeners) && !($listeners instanceof Traversable)) {
				continue;
			}
			foreach ($listeners as $listener) {
				if (!$listener instanceof CallbackHandler) {
					continue;
				}
				$sharedListeners[] = $listener;
			}
		}
		return $sharedListeners;
	}

	/**
	 * Внутренний метод.
	 * Объединение слушателей в PriorityQueue согласно приоритету
	 * Add listeners to the master queue of listeners. Used to inject shared listeners and wildcard listeners.
	 * @param  PriorityQueue $masterListeners
	 * @param  PriorityQueue $listeners
	 */
	protected function insertListeners($masterListeners, $listeners) {
		foreach ($listeners as $listener) {
			$priority = $listener->getMetadatum('priority');
			if (null === $priority) {
				$priority = 1;
			} elseif (is_array($priority)) {
				// If we have an array, likely using PriorityQueue. Grab first element of the array, as that's the actual priority.
				$priority = array_shift($priority);
			}
			$masterListeners->insert($listener, $priority);
		}
	}
}
