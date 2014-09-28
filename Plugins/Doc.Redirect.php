<?php

class IndexController extends AbstractActionController {
    function indexAction() {		
		/*
		 * Возвращает объект класса-плагина Zend\Mvc\Controller\Plugin\Redirect
		 */
		$this->redirect();
		
		/*
		 * Redirect::toRoute($route=null)
		 * Редирект на указанный route (определяется в конфигах модулей)
         * $route - имя ключа массива роута в конфиге.
		 * Если не задавать $route, то метод работает как refresh()
		 */
		$this->redirect()->toRoute('event-show-index');
		
		/*
		 * Redirect::toUrl($url)
		 * Редирект на указанный URL
		 */
		$this->redirect()->toUrl('http://www.zend.loc/plugins/index');
		
		/*
		 * Redirect::refresh()
		 * Обновить текущий route (редирект на текущий роут)
		 * Вызов метода toRoute(null)
		 */
		$this->redirect()->refresh();
				
	}
}

