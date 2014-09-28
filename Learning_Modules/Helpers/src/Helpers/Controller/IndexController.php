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
	
}