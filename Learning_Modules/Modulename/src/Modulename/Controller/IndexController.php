<?php
namespace Modulename\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 */
class IndexController extends AbstractActionController {

	/**
	 *
	 */
    function indexAction() {

    	return new ViewModel(
    			array()
    	);
    }

}