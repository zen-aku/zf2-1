<?php
namespace Services\Service;
/**
 * Сервис, регистрируемый в ServiceManager как ссылка на класс
 */
class InvokableService {

	function getInfo() {
		return "Сервис, регистрируемый в ServiceManager как ссылка на класс (в конфиге ключ invokables)";
	}
}