<?php
namespace ZendDb\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 */
class IndexController extends AbstractActionController {

	/**
	 * route: /zenddb/index/index
	 */
    function indexAction() {
		
    	return new ViewModel(
    			array()
    	);      
    }
    		
}