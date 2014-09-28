<?php
namespace HtmlGenerator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 */
class IndexController extends AbstractActionController {

	/**
	 * route: /htmlgenerator
	 */
    function indexAction() {

    	return new ViewModel(
    			array()
    	);
    }
	
}