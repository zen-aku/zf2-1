<?php
namespace TestMysqlGenerator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 */
class DriverController extends AbstractActionController {

	/**
	 * router "/testmysqlgenerator/sql/index"
	 */
    function indexAction() {
        
        $adapter = $this->getServiceLocator()->get('MysqlGenerator\Adapter\Adapter');
	
 			
    	return new ViewModel(
			array(
				//'result' => $result,
			)
    	);   
    }

}