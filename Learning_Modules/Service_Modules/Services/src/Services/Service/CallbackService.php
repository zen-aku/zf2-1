<?php
namespace Services\Service;

/**
 * Сервис, вызываемый с помощью фабрики сервиса ServiceFactory (или callback в конфиге), зарегистрированной в ServiceManager
 */
class CallbackService {

	private $getGreeting = null;

	function __construct( $s ) {
		$this->getGreeting = $s;
	}

	function getInfo() {
		$str = $this->getGreeting . " Сервис, созданный с помощью зарегистрированной в ServiceManager фабрики ServiceFactory (или callback в конфиге)";
		return $str;
	}

}
