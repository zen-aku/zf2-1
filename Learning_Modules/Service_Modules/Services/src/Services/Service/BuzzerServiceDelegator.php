<?php
namespace Services\Service;

use Zend\EventManager\EventManagerInterface;

/**
 * Декоратор сервиса Services\Service\BuzzerService, который c помощью фабрики BuzzerServiceDelegatorFactory или (callback-фабрики в сервис-конфиге)
 * декорирует метод buzz() сервиса Services\Service\BuzzerService
 */
class BuzzerServiceDelegator extends BuzzerService {

	protected $realBuzzer;		// BuzzerService
	protected $eventManager;	// EventManagerInterface

	function __construct( BuzzerService $realBuzzer, EventManagerInterface $eventManager ) {
		$this->realBuzzer   = $realBuzzer;
		$this->eventManager = $eventManager;
	}

	// декорируем метод buzz() класса Services\Service\BuzzerService
	function buzz(){

		$resultTrigger = $this->eventManager->trigger('buzz', $this)->last();
		return $resultTrigger . $this->realBuzzer->buzz();
	}
}