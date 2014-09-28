<?php
/*
Инициировать событие - значит вызвать всех слушателей, которые были зарегистрированы для данного события.
Весь процесс происходит последовательно. Каждый слушатель вызывается индивидуально,
и только после полной обработки обрабатывается следующий слушатель.
Последовательность обработки обусловлена приоритетом слушателя, который может быть объявлен для слушателя в методе attach(),
или последовательностью, в которой слушатели были добавлены.

В метод trigger() в качестве события могут быть переданы:
	- строка, которая представляет собой имя соответствующего события.
		Тогда внутри метода trigger() будет создан объект события Zend\EventManagerEvent('имя события') и в дальнейшем передан слушателю.
	- объект события Zend\EventManagerEvent('имя события'):
		Cоздать самому объект события и передать в trigger(), что по сути аналогично первому:

	$this->eventManager->trigger('getGreeting');

	$event = new Zend\EventManager\Event('getGreeting');
	$this->eventManager->trigger($event);

Bызов trigger() возвращает результат типа ResponseCollection (см. ниже), а именно: все то,
что вызванные слушатели ранее вернули индивидуально, возвращается в виде стэка ResponseCollection.
С помощью методов first() и last() (класса ResponseCollection) можно получить доступ к наиболее важным
возвращаемым значениям, и проверить наличие конкретного возвращаемого значения в коллекции – с помощью contains($value).
ResponseCollection - стэк, поэтому результаты выполнения слушателей будут представленя в обратном порядке выполнения слушателей:
первый - тот слушатель, который был выполнен последним.
Это можно использовать для проверки результатов выполнения слушателями при тестировании.

Алгоритм выполнения события:
	- в trigger($event) создаётся объект события $e = Event($event) (если он не был передан напрямую в $event)
		и передаётся вместе с именем события в triggerListeners($event, $e)
	- в triggerListeners($event, $e) последовательно запускаются в foreach все слушатели из $this->events[$event] и
		результаты сохраняются в стэке $responses:

			$responses = new ResponseCollection; 		// SplStack
			$listeners = $this->getListeners($event);	// $this->events[$event];
			foreach ($listeners as $listenerCallback)
				$responses->push(call_user_func($listenerCallback, $e)); // в callback-функцию $listenerCallback передаётся объект события $e

Исходя из логики работы trigger() важно, чтобы callback-функции слушателей возвращали какой-нибудь результат выполнения!

Метод triggerUntil($event, $target, $argv = array(), $callback = null) - полный аналог метода trigger(), но с обязательным вторым параметром $target.
Предполагалось, что при наличии 4-го параметра $callback  возвращаемые значения от слушателей будут попадать в $callback, и если возвращается TRUE, то слушание прерывается.
Это можно протестировать используя $result->stopped(). Но эту же возможность имеет и trigger(), поэтому нет особого смысла применять triggerUntil() вместо trigger(),
разве только для того чтобы названием вызова (triggerUntil) обозначить, что результат слушателя будет проверяться в $callback.

*/
// методы в немного урезанном виде (для понимания)
class EventManager implements EventManagerInterface {

	// array Array of PriorityQueue objects events['eventname'] = array('listenerCallback1', ...)
	protected $events = array();
	/**
	 * @param  string|EventInterface $event
	 * @param  string|object $target 	место, где само событие было инициировано
	 * @param  array|ArrayAccess $argv 	массив параметров, который передаётся в callback-слушателя внутри объекта события Event $e (свойство объекта $e->setParams($argv))
	 * @param  null|callable $callback  callback-функция, которая использует в качестве аргумента результат выполнения последнего слушателя
	 * @return ResponseCollection 		резельтаты выполнения слушателей в виде стэка ResponseCollection
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
			$e = new $this->eventClass(); // $e = new Zend\EventManager\Event()
			$e->setName($event);
			$e->setTarget($target);
			$e->setParams($argv);
		}
		// Initial value of stop propagation flag should be false
		$e->stopPropagation(false);
		return $this->triggerListeners($event, $e, $callback);
	}

	/**
	 * @param  string $event Event name
	 * @param  EventInterface $e
	 * @param  null|callable    $callback
	 * @return ResponseCollection
	 */
	function triggerListeners($event, EventInterface $e, $callback = null){

		$responses = new ResponseCollection; 		// SplStack
		$listeners = $this->getListeners($event);	// $this->events[$event];
		/*...*/
		foreach ($listeners as $listener) {
			$listenerCallback = $listener->getCallback();
/*!!!
			 * Trigger the listener's callback, and push its result onto the response collection
			 * Передаётся в callback-слушателя объект события EventInterface $e и запускается выполнение callback-слушателя
			 * Результат выполнения добавляется в стэк ResponseCollection $responses
			 */
			 $responses->push(call_user_func($listenerCallback, $e));

			// If the event was asked to stop propagating, do so
			if ($e->propagationIsStopped()) {
				$responses->setStopped(true);
				break;
			}
		}
		// If the result causes our validation callback to return true, stop propagation
		if ($callback && call_user_func($callback, $responses->last())) {
			$responses->setStopped(true);
			break;
		}
		return $responses;
	}
}
/**
 * Collection of signal handler return values
 */
class ResponseCollection extends SplStack {
	protected $stopped = false;
	/**
	 * Did the last response provided trigger a short circuit of the stack?
	 * @return bool
	 */
	public function stopped() {
		return $this->stopped;
	}
	/**
	 * Mark the collection as stopped (or its opposite)
	 * @param  bool $flag
	 * @return ResponseCollection
	 */
	public function setStopped($flag){
		$this->stopped = (bool) $flag;
		return $this;
	}
	/**
	 * Convenient access to the first handler return value.
	 * @return mixed The first handler return value
	 */
	public function first(){
		return parent::bottom();
	}
	/**
	 * Convenient access to the last handler return value.
	 * If the collection is empty, returns null. Otherwise, returns value returned by last handler.
	 * @return mixed The last handler return value
	 */
	public function last() {
		if (count($this) === 0) {
			return null;
		}
		return parent::top();
	}
	/**
	 * Check if any of the responses match the given value.
	 * @param  mixed $value The value to look for among responses
	 * @return bool
	 */
	public function contains($value) {
		foreach ($this as $response) {
			if ($response === $value) {
				return true;
			}
		}
		return false;
	}
}



