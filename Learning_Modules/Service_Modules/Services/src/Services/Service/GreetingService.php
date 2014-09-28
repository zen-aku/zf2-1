<?php

namespace Services\Service;
/**
 * Kласс-сервис осуществляет вывод приветствия в зависимости от времени
 */
class GreetingService {

	// получить приветствие в зависимости от времени
	function getGreeting() {
		if ( date("H") <= 11 ) {
 			return "Good morning, world!";
 		} else if ( date("H") > 11 && date("H") < 17 ) {
	 		return "Hello, world!";
	 	} else {
		 	return "Good evening, world!";
	 	}
	}
}