<?php
namespace DbGen\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use DbGenerator\Mysql\Test;
/**
 *
 */
class IndexController extends AbstractActionController {

	/**
	 * router "/dbgen/index/index"
	 */
    function indexAction() {
        
        $adapter = $this->getServiceLocator()->get('dbgenerator\adapter\adapter');
        
        //$test = new Test();
        //$test->show();

    	return new ViewModel(
    			array()
    	);
    }

}