<?php
namespace Helloworld\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * namespace контроллера определяется названием папки контроллеров данного модуля в src/Helloworld и
 * и относительным путём от неё: Helloworld\Controller
 * Контроллеры именуются [Classname]Controller
 * Методы именуются [methodname]Action
 */
class IndexController extends AbstractActionController {

	/**
	 * Объект сервис-класса (GreetingService), зарегистрированного в ServiceManager
	 * Внедряется с помощью фабрики сервисов/контроллеров IndexControllerFactory
	 * @var object
	 */
	private $greetingService;

    function indexAction() {

    	/* 1.
    	 * Вывод после прямого вызова 'invokables' index-контроллера.
    	 * module.config.php:
         * 'invokables' => array( 'Helloworld\Controller\Index' => 'Helloworld\Controller\IndexController')
		 *
         * Как правило, Action заканчивает свою работу путем предоставления результатов операций в модель представления ViewModel
         * Для каждого действия существует обычно ровно одно представление (шаблон представления), имя которого совпадает с именем Action:
         * view/[имя_модуля]/[имя_контроллера]/[имя_action].phtml - view/helloworld/index/index.phtml
         */
       	//return new ViewModel(array('greeting' => 'hello, world!'));

    	/*
    	 * Если вместо ViewModel возвращается объект Response, последующая деятельность по визуализации опускается.
    	 * Этот механизм полезен, если пользователь решит, например, кратко вернуть 503 код в случае,
    	 * если приложение просто проходит плановое техническое обслуживание, или когда необходимо вернуть данные опеделенного MIME - типа,
    	 * например, содержимое изображения, PDF документ или что-либо подобное.
    	 */
		/*
    	$resp = new \Zend\Http\PhpEnvironment\Response;
    	$resp->setStatusCode(503);
    	return $resp;
		*/


    	/* 2.
    	 * Вывод после прямого вызова 'invokables' index-контроллера с использованием сервиса 'greeting'
    	 * Запрашиваем сервис 'greetingService' из хранилища ServiceManager - запрашиваем объект класса Helloworld\Service\GreetingService
    	 * Конфигурация сервиса 'greetingService'(директория класса, котрый выступает сервисом) была задана в config\module.config.php или в Module.php
    	 */
    	// getServiceLocator() - метод AbstractActionController возвращает объект класса ServiceManager данного модуля
        //$greetingSrv = $this->getServiceLocator()->get('greetingService');
       	//return new ViewModel( array('greeting' => $greetingSrv->getGreeting()) );


		/* 3.
		 * Вывод после после вызова 'factories' index-контроллера из фабрики IndexControllerFactory, внедряющей сервис 'greeting' в index-контроллер.
		 *  module.config.php:
		 * 'factories' => array('Helloworld\Controller\Index' => 'Helloworld\Controller\IndexControllerFactory'
		 * 4.
		 * Вывод после после вызова index-контроллера c помощью обратного 'callback' вызова фабрики, которая реализована в анонимной функции в module.config.php
		 */
       	return new ViewModel( array('greeting' => $this->greetingService->getGreeting()) );


    	/* 2-3(4). Резюме
    	 * В варианте 3. контроллер IndexController не зависит от сервиса (и от ServiceManager), к которым он активно обращается
		 * Создание объекта сервиса-класса вынесено из IndexController в отдельный класс-фабрику
    	 * Если понадобится подключить не 'greetingService', а напр. 'greetingService2' при определённых условиях,
    	 * то для случая 2. прийдётся это условие прописывать (возможно св switch если много вариантов сервисов):
    	 * 		switch ($flag) {
    	 * 			case ('service1'): $service = $this->getServiceLocator()->get('Service1'); break;
    	 * 			case ('service2'): $service = $this->getServiceLocator()->get('Service2'); break;
    	 *		return new ViewModel( array('greeting' => $service->getGreeting()) );
    	 * Вариант 2 надо использовать только когда не предполагается изменение подключаемого сервиса.
    	 *
    	 * В варианте 2. вызов сервиса(создание объекта-класса сервиса) привязан к indexAction() и если прийдётся использовать этот сервис и в других
    	 * экшонах, то прийдётся в них тоже прописывать вызов этого сервиса. Тогда можно прописать вызов сервиса в конструкторе и внедренить его в свойство
    	 * класса, но тогда мы получим создание свойства-объекта и при неиспользовании сервиса. Фабрика сервиса внедряет сервис в свойство класса по запросу и
    	 * создаёт доступ к этому сервису из всех экшонов класса.  Из этого вывод: если подключаемый сервис не предполагается использовать в других экшонах
    	 * и не предполагается его изменение другим сервисом, то надо использовать 'invokables' сервис, в противном случае 'factories' сервис.
         */

    }

    /**
     * Метод ("Setter") для внедрения зависимостей.
     * Вызывается из IndexControllerFactory для внедрения  объекта класса-сервиса $service в контроллер IndexController
     * Используется при выводе 3 после после вызова index-контроллера из фабрики (см выше)
     * @param  $service - объект класса-сервиса
     */
    function setGreetingService( $service ) {
   		$this->greetingService = $service;
   	}

}
