<?php

namespace Services\Service;

/**
 * Сервис, который подвергнется декорации классом-делегатором BuzzerServiceDelegator
 * c помощью фабрики BuzzerServiceDelegatorFactory или callback-фабрики в сервис-конфиге
 */
class BuzzerService {

	function buzz() {
		return 'Buzz!';
	}

}