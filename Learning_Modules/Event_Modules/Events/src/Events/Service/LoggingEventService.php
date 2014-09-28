<?php

namespace Events\Service;

/** 
 * Сервис логгирования событий
 */
class LoggingEventService {
    
    private $log = array();     // log[] => Event
    
    /**
     * Добавить событие (см. GreetingServiceFactory.php) в лог-массив $this->log[] = $event
     * @param Event $event
     */
    function addEventLog($event) {
        $this->log[] = $event;
    }
     
    function getLog() {
        return $this->log;
    }
       
}
