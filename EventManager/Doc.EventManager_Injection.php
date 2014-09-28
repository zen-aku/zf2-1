<?php
/**
 * По-умолчанию Zend Framework 2 MVC регистрирует инициализатора (какой инициализатор и где он находится?),
 * который инъекцирует экземпляр EventManager (EventManagerInterface $eventManager) в любой класс,
 * реализующий Zend\EventManager\EventManagerAwareInterface.
 * Поэтому, если есть необходимость в доступе к EventManager из какого-то пользовательского класса,
 * то необходимо имплементрировать его от EventManagerAwareInterface и реализовать его метод setEventManager(EventManagerInterface $eventManager)
 * EventAwareInterface - можно перевести как 'знающий о EventManager'
 */

//namespace Zend\EventManager;
/**
 * Interface providing events that can be attached, detached and triggered.
 */
interface EventsCapableInterface {
	/**
	 * Retrieve the event manager Lazy-loads an EventManager instance if none registered.
	 * @return EventManagerInterface
	 */
	function getEventManager();
}
/**
 * Interface to automate setter injection for an EventManager instance
 */
interface EventManagerAwareInterface extends EventsCapableInterface {
	/**
	 * Inject an EventManager instance
	 * @param  EventManagerInterface $eventManager
	 * @return void
	 */
	function setEventManager(EventManagerInterface $eventManager);
}

/*
 * Простой пример. Наследники этого класса будут иметь доступ к EventManager через унаследованный метод $this->getEventManager()
 * Контроллеры модулей надо наследовать от AbstractActionController, который в свою очередь наследуется от AbstractController!!!,
 * а тот имплементирует EventManagerAwareInterface и реализует его методы.
 * И таким образом в контроллеры инъекцируется EventManager в свойство $events
 * и они имеют доступ к нему либо напрямую через свойство $this->events (что не надо делать)
 * или через метод $this->getEventManager() (что правильнее)
 */

// use Zend\EventManager\EventManagerAwareInterface;
// use Zend\EventManager\EventManagerInterface;
// use Zend\EventManager\EventManager;

class MyClass implements EventManagerAwareInterface {
	protected $events;

	function setEventManager(EventManagerInterface $eventManager) {
		// For convenience, this method will also set the class name / LSB name as
		// identifiers, in addition to any string or array of strings set to the $this->eventIdentifier property.
		// Связывание менеджера событий с классом (организация класса Observable или SplSubject)
		$eventManager->setIdentifiers(array( __CLASS__, get_called_class(), ));
		$this->events = $eventManager;
		return $this;
	}

	function getEventManager() {
		if (null === $this->events)
			$this->setEventManager(new EventManager());
		return $this->events;
	}

	function action() {
		// ...
		$eventManager = $this->getEventManager()->...;
		// ...
	}
}

/*
 * В ZF2 есть трейт, реализующий интерфейсы EventManagerAwareInterface и EventsCapableInterface
 * И таким образом, упрощается реализация интерфейсов в собственном классе.
 */
trait EventManagerAwareTrait {
	/**
	 * @var EventManagerInterface
	 */
	protected $events;
	/**
	 * Set the event manager instance used by this context.
	 * For convenience, this method will also set the class name / LSB name as
	 * identifiers, in addition to any string or array of strings set to the $this->eventIdentifier property.
	 * @param  EventManagerInterface $events
	 * @return mixed
	 */
	function setEventManager( EventManagerInterface $events ) {
		$identifiers = array(__CLASS__, get_class($this));
		if (isset($this->eventIdentifier)) {
			if ((is_string($this->eventIdentifier))
					|| (is_array($this->eventIdentifier))
					|| ($this->eventIdentifier instanceof Traversable)
			) {
				$identifiers = array_unique(array_merge($identifiers, (array) $this->eventIdentifier));
			} elseif (is_object($this->eventIdentifier)) {
				$identifiers[] = $this->eventIdentifier;
			}
			// silently ignore invalid eventIdentifier types
		}
		$events->setIdentifiers($identifiers);
		$this->events = $events;
		if (method_exists($this, 'attachDefaultListeners')) {
			$this->attachDefaultListeners();
		}
		return $this;
	}
	/**
	 * Retrieve the event manager Lazy-loads an EventManager instance if none registered.
	 * @return EventManagerInterface
	 */
	function getEventManager() {
		if (!$this->events instanceof EventManagerInterface) {
			$this->setEventManager(new EventManager());
		}
		return $this->events;
	}
}

/*
 * И наш пример с трейтом
 */

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

class MyClass implements EventManagerAwareInterface {

	use EventManagerAwareTrait;

	function action() {
		// ...
		$eventManager = $this->getEventManager()->...;
		// ...
	}
}

