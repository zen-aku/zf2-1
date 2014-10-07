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
	
    /**
	 * Пример с хелпером headstyle()
	 * route: /helpers/index/headstyle
	 */
    function headstyleAction() {
		return new ViewModel(
			array()
		);
	}
	
	/**
	 * Пример с хелпером headlink()
	 * route: /helpers/index/headlink
	 */
    function headlinkAction() {
		return new ViewModel(
			array()
		);
	}
	
	/**
	 * Пример с хелпером headmeta()
	 * route: /helpers/index/headmeta
	 */
    function headmetaAction() {
		return new ViewModel(
			array()
		);
	}
    
    /**
	 * Пример с хелпером headscript() и inlinescript()
	 * route: /helpers/index/headscript
	 */
    function headscriptAction() {
		return new ViewModel(
			array()
		);
	}
    
    /**
	 * Пример с хелперамим html: htmlList(), htmlObject(), htmlFlash(), htmlPage(), htmlQuicktime()
	 * route: /helpers/index/html
	 */
    function htmlAction() {
		return new ViewModel(
			array()
		);
	}
    
    /**
	 * Пример с хелпером gravatar()
	 * route: /helpers/index/gravatar
	 */
    function gravatarAction() {
		return new ViewModel(
			array()
		);
	}
    
    /**
	 * Пример с хелперамим escape: escapeHtml(), escapeHtmlAttr(), escapeCss(), escapeJs(), escapeUrl()
	 * route: /helpers/index/escape
	 */
    function escapeAction() {
		return new ViewModel(
			array()
		);
	}
    
    /**
	 * Пример с хелпером basePath()
	 * route: /helpers/index/basepath
	 */
    function basepathAction() {
		return new ViewModel(
			array()
		);
	}
	
	/**
	 * Пример с хелпером cycle()
	 * route: /helpers/index/cycle
	 */
    function cycleAction() {
		return new ViewModel(
			array()
		);
	}
    
    /**
	 * Пример с хелпером declareVars()
	 * route: /helpers/index/declarevars
	 */
    function declarevarsAction() {
		return new ViewModel(
			array()
		);
	}
    
    /**
	 * Пример с хелпером flashMessenger()
	 * route: /helpers/index/flashmessenger
	 */
    function flashmessengerAction() {
        
        // добавить сообщения дефолтным именам хранилищ сообщений
        $this->flashMessenger()
			->addMessage('DefaultMessage')
			->addInfoMessage('InfoMessage')
			->addSuccessMessage('SuccessMessage')
			->addWarningMessage('WarningMessage')
			->addErrorMessage('ErrorMessage'); 
        
        // добавить сообщение с ключом 'mynamespace', с которым онобудет храниться в хранилище
        $this->flashMessenger()->setNamespace('mynamespace')->addMessage('MynamespaceMessage');
        
		return new ViewModel(
			array()
		);
	}
	
	/**
	 * Пример с хелпером identity()
	 * route: /helpers/index/identity
	 */
    function identityAction() {
		return new ViewModel(
			array()
		);
	}
	
	/**
	 * Пример с хелпером json()
	 * route: /helpers/index/json
	 */
    function jsonAction() {
		return new ViewModel(
			array()
		);
	}
	
	/**
	 * Пример с хелпером layout()
	 * route: /helpers/index/layout
	 */
    function layoutAction() {
		return new ViewModel(
			array()
		);
	}
    
    /**
	 * Пример с хелпером partial()
	 * route: /helpers/index/partial
	 */
    function partialAction() {
		return new ViewModel(
			array()
		);
	}
	
	/**
	 * Пример с хелпером url()
	 * route: /helpers/index/url
	 */
    function urlAction() {
		return new ViewModel(
			array()
		);
	}
    
}