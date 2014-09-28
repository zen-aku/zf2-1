<?php
namespace Services\Service;
/**
 * Сервис, регистрируемый в ServiceManager как объект (в конфиге ключ services)
 * Имплементируем его от моего интерфейса Services\Service\InitServiceInterface
 * для инициализации этого сервиса с помощью моего инициализатора Services\Service\Initializer,
 * зарегистрированного в ServiceManager initializers (см в конфиге сервисов)
 */
class InitService implements InitServiceInterface {

	private $init;

	/**
	 * @see \Services\Service\InitServiceInterface::setInit()
	 */
	function setInit( $in ) {
		$this->init = $in;
	}

	function getInfo() {
		return $this->init." Сервис, проинициализировнный инициализатором Initializer во время вызова";
	}
}