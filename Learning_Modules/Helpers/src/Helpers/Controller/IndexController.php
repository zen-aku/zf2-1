<?php
namespace Helpers\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 */
class IndexController extends AbstractActionController {

	/**
	 * route: /helpers/index
	 */
    function indexAction() {
		
    	return new ViewModel(
			array()
    	);
    }
	
	/**
	 * Пример с хелпером doctype()
	 * route: /helpers/index/doctype
	 */
    function doctypeAction() {
		return new ViewModel(
			array()
		);
	}
	
	/**
	 * Пример с хелпером placeholder()
	 * route: /helpers/index/placeholder
	 */
    function placeholderAction() {		
		$title = 'Примеры с Placeholder';
		$content = "Хелпер-контейнер placeholder() спользуется для сохранения содержимого между скриптом вида и отображением. 
			placeholder()->_invoke(key) возвращает контейнер класса Zend\View\Helper\Placeholder\Container\AbstractContainer extends ArrayObject 
			из своего свойства массива с ключом key: this->items['foo'] = <AbstractContainer(value)>"; 
		
    	return new ViewModel(
			array(
				'data' => ['title' => $title, 'content' => $content],
			)
    	);
	}
	
	/**
	 * Пример с хелпером headtitle()
	 * route: /helpers/index/headtitle
	 */
    function headtitleAction() {
		return new ViewModel(
			array()
		);
	}
	
}