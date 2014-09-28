<?php

namespace Events\Listner;

use Zend\EventManager\SharedEventManagerInterface;

/**
 * Свойство protected $listeners = array(). 
 * Реализация метода detachShared() интерфейса SharedListenerAggregateInterface
 */
trait SharedListnerAggregateTrait {

	protected $listeners = array();

	function detachShared( SharedEventManagerInterface $sharedEventManager ) {
		foreach ($this->listeners as $id => $listner) {
			if ($sharedEventManager->detach($id, $listner))
				unset($this->listeners[$id]);
		}
	}
}

