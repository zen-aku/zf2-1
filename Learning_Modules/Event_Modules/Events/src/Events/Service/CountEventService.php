<?php

namespace Events\Service;

/**
 * Сервис подсчёта событий
 */
class CountEventService {
    /**
     * @var int
     */
    private $count = 0;
    
    function count() {
        $this->count++;
    }
    
    function getCount() {
        return $this->count;
    }
    
}