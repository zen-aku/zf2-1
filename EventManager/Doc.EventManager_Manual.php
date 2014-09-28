<?php
/*
 * Структурные единицы паттерна:
 *
 * 1) Объект события (event - $event):
 * 	- string - название события (имя метода __FUNCTION__)
 *	- array - массив имён событий
 *	- ListenerAggregateInterface - объект, содержащий объекты событий и слушателей
 */
	$greetingService->getEventManager()->attach( __FUNCTION__, function($e){ });
	$greetingService->getEventManager()->attach( 'get', function($e){ });
	$greetingService->getEventManager()->attach( array('get1', 'get2'), function($e){ });
	$greetingService->getEventManager()->attach(new MyGetGreetingEventListenerAggregate());
	// любое событие будет уведомлять слушателя $callback
	$greetingService->getEventManager()->attach( '*', $callback);

/*
 * 2) Слушатели событий (listeners - $callback):
 *	- callable - любая функция обратного вызова (callback).
 *		Можно использовать любое валидное имя функции,
 *		объект Closure,
 *		строка ссылающаяся на статический метод:
 *			array callback with a named static method or instance method: array('class', 'method')
 *
 * Слушатели, которые зарегистрированы для события, информируются (о наступлении события) в порядке очередности.
 * "Информируются" означает, что зарегистрированные обратное вызовы будут вызваны в порядке очередности.
 */
	function foo(){}

	$greetingService->getEventManager()->attach('get', 'foo');
	$greetingService->getEventManager()->attach('get', function($e){ });
	$greetingService->getEventManager()->attach('get', array('class', 'method'));
/*
 * Слушатель $callback будет зарегистрирован для конкретного события $event в свойстве-массиве EventManager::events[$event][]=>callback-listner
 * Возвращает	СallbackHandler-объект слушателя $callback, прикреплённого к событию $event:
 */
	EventManager::attach($event, $callback = null, $priority = 1) {
		/*...*/
		$listener = new CallbackHandler($callback, array('event' => $event, 'priority' => $priority));
		// добавить в очередь PriorityQueue $this->events слушателя
		$this->events[$event]->insert($listener, $priority);
		return $listener;
	}

// Чтобы удалить слушателя, надо передать методу detach() 	СallbackHandle-объект слушателя:

	EventManager::detach($listener) {
		/*...*/
		$this->events[$event]->remove($listener);
	}
/*
 * Поэтому при регистрации слушателя сохраняют его CallbackHandler в переменой
 * и в случае необходимости передают его в метод удаления:
 */
	$listner = $greetingService->getEventManager()->attach('get', array('class', 'method'));
	/*...*/
	$greetingService->getEventManager()->detach($listener);

/*
 * Чтобы зарегистрировать несколько слушателей для одного события или даже одновременно для нескольких событий
 * используют ListenerAggregateInterface (см. Doc.ListnerAggregate.php).
 */

/*
 * 3) Инициатор события (triggers)
 * Вызов для инициирования события, если EventManager внедрён в свойство объекта (см. Doc.EventManager_Injection):
 */
	$this->eventManager->trigger('event1');

// С помощью создания объекта Zend\EventManager\Event():

	$event = new Zend\EventManager\Event('getGreeting');
 	$this->eventManager->trigger($event);

/*
 * Таким образом вызываются все слушатели, которые были зарегистрированы для данного события.
 * Весь процесс происходит последовательно. Каждый слушатель вызывается индивидуально,
 * и только после полной обработки обрабатывается следующий слушатель.
 * Последовательность обработки обусловлена приоритетом слушателя, который может быть объявлен для слушателя в методе attach(),
 * или последовательностью, в которой слушатели были добавлены.
 *
 * В EventManager->trigger($event, $target, $params) из переданного $event (string|Event) создаётся объект Event $e,
 * загоняется в него массив $params и передаётся в callback-слушателя в качестве аргумента (см. Doc.triggerEventManager.php и Doc.Event.php):
 * Подробно о trigger() см. Doc.triggerEventManager.php
 */
	$greetingService->getEventManager()->attach( 'get', function(EventInterface $e){ });

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

// Создать объект класса Foo с внедрённым в него свойством объектом класса EventManager благодаря use Zend\EventManager\EventManagerAwareTrait
$foo = new Foo();

// Создаём событие 'bar' и прикрепляем к нему callback-слушателя
$foo->getEventManager()->attach('bar', function ($e) use () {
	$event  = $e->getName();
	$target = get_class($e->getTarget());
	$params = json_encode($e->getParams());
	echo "event: $event target:$target params:$params";
	return true;
});

// При вызове метода bar() запустится trigger(__FUNCTION__), который вызовет callback-слушателя
$foo->bar('baz', 'bat');	// выведет event:... target:... params:...



