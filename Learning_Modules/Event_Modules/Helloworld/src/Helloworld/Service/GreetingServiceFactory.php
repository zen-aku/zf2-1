<?php
namespace Helloworld\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * LoggingService может быть запрошен из сервис-менеджера с помощью ключа loggingService.
 * Теперь мы должны дополнительно обеспечить,чтобы GreetingService мог инициировать событие,
 * на основе которого может быть запущен метод onGetGreeting сервиса LoggingService.
 * Чтобы предоставить для GreetingService EventManager и одновременно зарегистрировать
 * выполнение метода onGetGreeting сервиса LoggingService, мы сделаем фабрику для GreetingService
 */
class GreetingServiceFactory implements FactoryInterface {

    /**
     * Первоначально, фабрика генерирует GreetingService, который она, в конечном итоге, возвращает.
     * Предварительно фабрика размещает EventManager в GreetingService, так что
     * GreetingService может теперь также инициировать события и управлять слушателями.
     * А затем LoggingService и его метод onGetGreeting() регистрируются для события getGreeting.
     */
	function createService( ServiceLocatorInterface $serviceLocator ) {
		$greetingService = new GreetingService();
		$greetingService->setEventManager( $serviceLocator->get('eventManager') );
		/*
		$loggingService = $serviceLocator->get('loggingService');
		$greetingService->getEventManager()->attach(
		    // вызвать метод сервиса LoggingService->onGetGreeting()
			'getGreeting', array($loggingService, 'onGetGreeting')
		);
		*/
		/*
		 * При использовании замыкания мы можем обеспечить, чтобы $loggingService,
		 * который запрашивается в приведенном выше примере непосредственно,
         * впервые запрашивался в фактический момент события с помощью "отложенной инициализации"
         * Преимущество очевидно: LoggingService генерируется фактически только тогда, когда он должен быть использован.
         * Действительно, событие getGreeting может просто никогда не состояться.
		 */
		$greetingService->getEventManager()->attach(
			'getGreeting',
			function($e) use($serviceLocator) {
			    // вызвать метод сервиса LoggingService->onGetGreeting() и передать ему объект события $e
				$serviceLocator->get('loggingService')->onGetGreeting($e);
			}
		);
		return $greetingService;
	}
}
	/*
	 * Если мы используем SharedServiceManager, можно еще больше упростить GreetingServiceFactory.
	 * Нужно обеспечить, чтобы в этом показательном примере, непосредственно в сервисе перед запуском события,
	 * соответствующий EventManager почувствовал ответственность за этот "идентификатор".
	 * Это можно сделать, используя метод addIdentifiers() в Helloworld\Service\GreetingService
	 */
/*
class GreetingServiceFactory implements FactoryInterface {

	function createService( ServiceLocatorInterface $serviceLocator ) {
	    // Создаём событие 'getGreeting' для класса GreetingService и навешиваем на него callback-слушателя
        $serviceLocator->get('sharedEventManager')->attach(
            'GreetingService',
            'getGreeting',
            function($e) use($serviceLocator) {
                $serviceLocator->get('loggingService')->onGetGreeting($e);
            }
		);

        $greetingService = new GreetingService();
		return $greetingService;
	}
}
*/