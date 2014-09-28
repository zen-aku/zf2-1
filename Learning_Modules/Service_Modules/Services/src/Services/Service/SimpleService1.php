<?php
namespace Services\Service;

/**
 * Сервис, вызываемый с помощью абстрактной фабрики сервисов AbstractFactoryService,
 * зарегистрированной в ServiceManager
 */
class SimpleService1 {

	private $getGreeting = null;

	function __construct( $s ) {
		$this->getGreeting = $s;
	}

	function getInfo() {
		$str = $this->getGreeting . " Сервис 'simpleService1', созданный с помощью зарегистрированной в ServiceManager абстрактной фабрики";
		return $str;
	}

}