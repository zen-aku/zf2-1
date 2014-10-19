<?php
namespace ZendDb\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Db\Adapter\Adapter;

/**
 *
 */
class IndexController extends AbstractActionController {

	/**
	 * route: /zenddb/index/index
	 */
    function indexAction() {
		
		/*
		 * Получаем доступ к адаптеру подключения к бд Zend\Db\Adapter\Adapter через сервис-манагер
		 * Сервис 'Zend\Db\Adapter\Adapter' зарегистрирован в сервис-манагере в global.php
		 */
		$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		
    	return new ViewModel(
    			array()
    	);
    }
		
}