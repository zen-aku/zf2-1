<?php
namespace TestMysqlGenerator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 */
class SqlController extends AbstractActionController {

	/**
	 * router "/testmysqlgenerator/sql/index"
	 */
    function indexAction() {
        
        $adapter = $this->getServiceLocator()->get('MysqlGenerator\Adapter\Adapter');
		
		//$sql = new \MysqlGenerator\Sql\Sql($adapter);
        //$sql = $this->getServiceLocator()->get('Zend\Db\Sql\Sql');
		 
		
		
    	return new ViewModel(
			array(
				//'result' => $result,
			)
    	);   
    }

}