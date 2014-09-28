<?php

class IndexController extends AbstractActionController {
    function indexAction() {
		
		/*
		 * Возвращает объект класса-плагина Zend\Mvc\Controller\Plugin\Url
		 */
		$this->url();
		
		/*
		 * Url::fromRoute($route = null, $params = array(), $options = array(), $reuseMatchedParams = false)
		 * Генерирует Url из $route, $params и $options
		 * $route - имя ключа массива роута в конфиге
		 */
		$url = $this->url()->fromRoute('event-show-index');
		
	}
}
