<?php
/*
 * Унаследованные методы от AbstractController предоставляют доступ из контроллера через его методы
 * к ServiceManager, EventManager, PluginManager, Request и Response
 * (AbstractController внедряет в своих наследников ServiceManager, EventManager, PluginManager, Request и Response)
 *
 * Контроллеры модулей надо наследовать от AbstractActionController, который в свою очередь наследуется от AbstractController!!!,
 * а тот имплементирует ServiceLocatorAwareInterface и реализует его методы setServiceLocator() и getServiceLocator().
 * Благодаря имплементации от ServiceLocatorAwareInterface, инициализатор Zend Framework 2 MVC инъекцирует экземпляр ServiceManager (ServiceLocatorInterface $serviceLocator) в свойство контроллера.
 * И таким образом в контроллере содержится ServiceManager в свойстве serviceLocator и к нему имеется доступ либо напрямую через свойство $this->serviceLocator (что ненадо делать)
 * либо через метод $this->getServiceLocator() (что правильнее)
 */

// namespace Zend\Mvc\Controller;
abstract class AbstractController implements
	Dispatchable,
	EventManagerAwareInterface,
	InjectApplicationEventInterface,
	ServiceLocatorAwareInterface 		// предоставляет доступ к ServiceManager
{

	$plugins;		// PluginManager
	$request;		// Request
	$response;		// Response
	$event;			// Event
	$events;		// EventManagerInterface (EventManager)
	$serviceLocator;// ServiceLocatorInterface (ServiceManager)
	$eventIdentifier; // string

	// Отправить запрос с ответом (Dispatch a request)
	dispatch(Request $request, Response $response = null) return Response|mixed
	// Вернуть HttpRequest объект $this->request, если его нет то вернётся $this->request = new HttpRequest()
	getRequest() return HttpRequest $this->request
	// Вернуть HttpResponse объект $this->response, если его нет то вернётся $this->response = new HttpResponse()
	getResponse() return HttpResponse $this->response

	// Задать объект EventManager, используемый в данном контексте (модуле, контроллере)
	setEventManager(EventManagerInterface $events) return $this
	// Вернуть объект EventManager, используемый в данном контексте (контроллере)
	// Для Lazy-loads "ленивой загрузки" объекта EventManager (создание объекта EventManager без его предвапительной регистрации)
	getEventManager() return EventManagerInterface $this->events

	// Задать событие event используемое во время dispatch - отправки запроса-ответа
	// По умолчанию, будет отправляться в MvcEvent если другие event type уже доставлены
	setEvent(Event $e) return void
	// Вернуть событие event, заданное в setEvent(), если его нет, то будет создан и возвращён new MvcEvent
	getEvent() return MvcEvent $this->event

	// Задать объект ServiceManager - задаётся инициализатором MVC объект ServiceManager
	setServiceLocator(ServiceLocatorInterface $serviceLocator) return void
	// Вернуть объект ServiceManager
	getServiceLocator() return ServiceLocatorInterface $this->serviceLocator

	// Задать объект PluginManager
	setPluginManager(PluginManager $plugins) return $this
	// Вернуть объект PluginManager $this->plugins,  если его нет то вернётся $this->plugins = new PluginManager()
	getPluginManager() return PluginManager $this->plugins
	// Вернуть объект плагина из объекта PluginManager ($this->plugins) с названием $name и передать в его конструктор параметры null|array $options
	plugin($name, array $options = null) return object plugin
	// Вернуть плагин-функцию/метод по названию метода $method и передать в него параметры $params
	__call($method, $params) return mixed

	// Регистрировать дефолтные события для этого контроллера
	protected attachDefaultListeners() return void

	// Трансформировать "action" символ в method name
	getMethodFromAction($action) return string methodname
}
/*
 * Проверка Action и принудительное(?) выполнение запроса
 */
abstract class AbstractActionController extends AbstractController {

	// Выполнить запрос (принудительно?)
	onDispatch(MvcEvent $e) return mixed результат выполнения запроса

	// indexAction по умолчанию, если он не переопределён в контроллере
	indexAction() return new ViewModel(array('content' => 'Placeholder page'))

	// Вернуть createHttpNotFoundModel() или createConsoleNotFoundModel() если вызываемый экшен не найден
	notFoundAction() return createHttpNotFoundModel()|createConsoleNotFoundModel()

	// Создать HTTP view model с сообщением "not found" page. Выдаёт 404 статус ошибки
	protected createHttpNotFoundModel(HttpResponse $response) return new ViewModel(array('content' => 'Page not found'))
	// Создать console view model с сообщением "not found" action. Выдаёт консольную ошибку 1
	protected createConsoleNotFoundModel($response) return (new ConsoleModel)->setResult('Page not found')
}



