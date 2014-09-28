<?php

namespace Plugins\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Пользовательский плагин.
 * Должен быть унаследован от AbstractPlugin. 
 * Если не предполагается обращение к плагину как к объекту с последующим вызовом его методов,
 * то лучше его делать с __invoke()
 */
class CurrentDate extends AbstractPlugin {
    
    /**
     * Вернуть текущую дату
     */
    function __invoke() {
        return date('d.m.Y');
    }
    
}
