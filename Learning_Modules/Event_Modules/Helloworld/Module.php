<?php
namespace Helloworld;
/*
 * namespace этого класса должен совпадать с namespace модуля,
 * который определяется названием папки контроллеров в src/ : src/Helloworld
 */
class Module {

    /*
     * Возвращает конфигурацию автозагрузкика.
     * Первоначально будет опрошена карта классов (ClassMap), а при
     * необходимости будет использоваться механизм PSR-0 стандартного автозагрузкика,
     * если никаких совпадений не найдено до этого момента.
     */
    function getAutoloaderConfig() {
        return array(
            /*
             * Самая производительная автозагрузка - ClassMapAutoloade:
             * Если запрошен соответствующий класс, загрузчик ищет в массиве autoload_classmap.php соответствующее значение
             * и загружает файл. Недостаток : карта классов должна постоянно поддерживаться.
             */
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            /*
             * Конфигурация стандартного автозагрузкика соглашения PSR-0:
             *  имя класса должно транслироваться непосредственно в имя файла.
             *  Это относится к классам, которые используют "реальные пространства имен":
             *  ожидается, что у класса Translator, который будет определен в пространстве имен Zend,
             *  будет такое же расположение в файловой системе Zend/Translator.php
             */
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Альтернативный способ регистрации классов-сервисов в хранилище сервисов ServiceManager
     * вместо регистрации в файле module.config.php
     */
    function getServiceConfig() {
    	return array(
            // Данные сервисы доступны в качестве "invokable"(вызываемые), т.е. определяют класс, экземпляр которого может быть создан при необходимости.
            'invokables' => array(
    			// [название_сервиса] => [директория класса, который будет сервисом]
                'loggingService' => 'Helloworld\Service\LoggingService',
    		),
    	    'factories' => array(
                'greetingService' => 'Helloworld\Service\GreetingServiceFactory',
    	    ),
    	);
    }

}
