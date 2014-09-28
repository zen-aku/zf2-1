<?php
namespace Services\Service;
/**
 * Сервис, регистрируемый в ServiceManager как объект (в конфиге ключ services)
 */
class ObjectService {

	function getInfo() {
		return "Сервис, регистрируемый в ServiceManager как объект (в конфиге ключ services)";
	}
}