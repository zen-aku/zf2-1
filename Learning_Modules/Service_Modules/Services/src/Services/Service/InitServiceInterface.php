<?php
namespace Services\Service;

/**
 * Сервисы, реализующие InitServiceInterface будут проинициализированы классом-инициализатором
 * Services\Service\InitService, зарегистрированом в ServiceManager initialaizers (см. в конфиге)
 */
interface InitServiceInterface {

	/**
	 * Задаёт массив данных при инициализации в Services\Service\InitService
	 * @param mixed $init
	 */
	function setInit( $init );
}