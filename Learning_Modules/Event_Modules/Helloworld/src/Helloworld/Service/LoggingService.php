<?php
namespace Helloworld\Service;

/**
 * Предположим, что мы хотели бы делать запись в лог-файл всякий раз, когда генерируется дата, чтобы всегда
 * знать, как часто эта услуга вызывается в определенный промежуток времени.
 * Мы хотим вызвать onGetGreeting(), как только произойдет событие getGreeting сервиса GreetingService.
 */
class LoggingService {
    function onGetGreeting() {
		// Logging-Implementierung
	}
}