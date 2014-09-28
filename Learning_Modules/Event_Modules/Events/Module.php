<?php
namespace Events;

use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\EventManager\EventInterface;

require 'ModuleConfig.php';

class Module
	extends ModuleConfig
	implements InitProviderInterface,  BootstrapListenerInterface
{
	/**
	 * init() вызывается перед загрузкой модуля.
	 * Невозможно настроить поведение, зависимое от модуля, после загрузки всего приложения (т.е. после загрузки модулей).
	 * Поэтому, если надо выполнить какое-то действие, связанное с модулем (с методом какого-то его контроллера), после загрузки приложения
	 * то надо с помощью SharedEventManager прикрепить это действие к событию модуля и оно будет выполняться после загрузки модуля при вызове этого события.
	 */
	function init( ModuleManagerInterface $moduleManager ) {
		
		$sharedEvents = $moduleManager->getEventManager()->getSharedManager();

		/*
		 * Все контроллеры унаследованы от Zend\Mvc\Controller\AbstractController, который имеет метод dispatch(),
		 * который автоматически вызывается при обращении к контроллеру при запросе. 
		 * В этом методе всегда запускается триггер: $this->getEventManager()->trigger('dispatch', $e ) 
		 * для события 'dispatch' и слушателям этого события передаётся таргет Zend\Mvc\MvcEvent $e со всеми параметрами запроса.
		 * Таким образом, определяя __NAMESPACE__ (т.е. имя модуля) и событие 'dispatch' для SharedEventManager
		 * мы задаём, что после вызова любого контроллера модуля __NAMESPACE__ будут вызваны слушатели, 
		 * прикреплённые в SharedEventManager к событию 'dispatch'.
		 */		
		$sharedEvents->attach(
			__NAMESPACE__,
			'dispatch',
			function( $mvcEvent ) {
				/*
				 * $mvcEvent->getTarget() - возвращает объект, в котором было запущено(триггировано) событие,
				 * т.е. объект контроллера. И теперь можно обратиться к любому методу контроллера, передавая ему какие-то
				 * начальные параметры.  
				 */
				$controller = $mvcEvent->getTarget();
				/*
				 * Настроим другой макет для всех страниц, которые создаются с
				 * помощью экшенов контроллеров нашего модуля Events
				 * Шаблон прописывается в карте шаблонов в module.config.php в 'template_map' с одновременным созданием файла в указываемой директории
				 */
				$controller->layout('layout/EventsLayout');
			}
		);
		
		/*
		 * Аналогичным образом можно прикреплять слушателей к любому экшену контроллеров,
		 * дополнительно прописав триггирование события в соответствующих экшенах.
		 */	
		$sharedEvents->attach(
			'Events\Controller\SharedEventController',
			'initAction',
			function($event) {
				$controller = $event->getTarget();
				$serviceManager = $controller->getServiceLocator();
				$serviceManager->get('Events\Service\LoggingEventService')->addEventLog('initAction');
			}				
		);		
	}

	/**
	 * Вызывается onBootstrap когда ModuleManager уже завершил свою работу и вернул управление приложению (Application). 
	 * Таким образом, при использовании onBootstrap() доступны методы, сервисы и данные, которые еще не были доступны в init().
	 * Если init() в основном применяют для прикрепления слушателей к событиям, то onBootstrap() для вызова
	 * каких-то сервисов или конфигов.
	 * Из передаваемого объекта MvcEvent $e получаем доступ ко всем элементам фреймворка через объект Zend\Mvc\Application, получаемый из $e->getApplication()
	 * @param Zend\Mvc\MvcEvent $e 
	 */
	function onBootstrap( EventInterface $e ) {
		/*
		echo '<pre>';
		print_r($e);
		exit;
		*/		
		$application = $e->getApplication();
        $serviceManager = $application->getServiceManager();
		// вызвать сервис 'Events\Service\LoggingEventService' и добавить в лог запись 'onBootstrap' 
		$serviceManager->get('Events\Service\LoggingEventService')->addEventLog('onBootstrap');
	}
		 
		 
}