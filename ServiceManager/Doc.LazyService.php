<?php
/**
 * Используется паттерны Decorator+Proxy, когда сервисы замещаются(декорируются) прокси-сервисами из ProxyManager.
 * Т.е. при запросе сервиса из ServiceManager вызывается декорирующая фабрика LazyServiceFactory (заранее помещённая в ServiceManager),
 * которая создаёт объект сервиса и возвращает его, одновременно помещает его в proxy-хранилище ProxyManager(?).
 * При следующем запросе этого сервиса декоратор LazyServiceFactory будет брать уже готовый объект из proxy-хранилища ProxyManager
 * Это используется при частом вызове какого-то сервиса (напр. соединение с бд).
 *
 * LazyServiceFactory использут ProxyManager, который надо установить (https://github.com/Ocramius/ProxyManager):
 *	I:\OpenServer\domains\zend.loc>composer require ocramius/proxy-manager:*
 *	Дополнительные модули, расширяющие функционал ProxyManager:
 *	I:\OpenServer\domains\zend.loc>composer require ocramius/generated-hydrator:*
 *	Установятся в vendor:
 *	- ocramius/proxy-manager
 *	- ocramius/generated-hydrator
 *	- ocramius/code-generator-utils
 *	- nikic/php-parser
 */

namespace MyApp;
/**
 * Класс-сервис, создающийся с задержкой 5 сек
 */
class Buzzer {
	function __construct() {
		sleep(5);
	}

	function buzz() {
		return 'Buzz!';
	}
}

/**
 * Конфигурировать ServiceManager
 */
$config = array(
	'lazy_services' => array(
		// mapping services to their class names is required since the ServiceManager is not a declarative DIC
		'class_map' => array(
				'buzzer' => 'MyApp\Buzzer',
		),
	),
);
$serviceManager = new \Zend\ServiceManager\ServiceManager();
$serviceManager->setService('Config', $config);


// задать в ServiceManager Invokable-сервис 'buzzer'=>'MyApp\Buzzer'
$serviceManager->setInvokableClass('buzzer', 'MyApp\Buzzer');
// задать в ServiceManager декорирующую фабрику сервиса 'LazyServiceFactory'=>'Zend\ServiceManager\Proxy\LazyServiceFactoryFactory'
$serviceManager->setFactory('LazyServiceFactory', 'Zend\ServiceManager\Proxy\LazyServiceFactoryFactory');
// использовать декоратор 'LazyServiceFactory' при вызове сервиса 'buzzer'
$serviceManager->addDelegator('buzzer', 'LazyServiceFactory');



$buzzer = $serviceManager->get('buzzer');
echo $buzzer->buzz();	// 'Buzz!'


// благодаря проксированию, этот код будет отрабатывать не 100*5=500 сек на создание каждого сервиса, а 5сек на создание первого сервсиса, а остальные 99 будут вызываться из proxy
for ($i = 0; $i < 100; $i += 1) {
	$buzzer = $serviceManager->create('buzzer');

	echo "created buzzer $i\n";
}
echo $buzzer->buzz();





