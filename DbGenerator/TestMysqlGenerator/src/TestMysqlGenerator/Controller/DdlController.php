<?php
namespace TestMysqlGenerator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use MysqlGenerator\Sql;

class DdlController extends AbstractActionController {

	/**
	 * router "/testmysqlgenerator/ddl/index"
	 */
    function indexAction() {
        
        $adapter = $this->getServiceLocator()->get('MysqlGenerator\Adapter\Adapter');   
        
		/*
		 * Два способа выполнения мультизапроса :
		 *  - пошаговый через формировани отдельных строк запроса через Adapter::getSqlStringForSqlObject() и объединение их в одну строку через разделитель ';'
		 *  - быстрый через передачу в Adapter::execSqlObject() массива sql-объектов мультизапроса
		 */
		/*
		$query1 = $adapter->getSqlStringForSqlObject(new Sql\DropTable('book'));
		$query2 = $adapter->getSqlStringForSqlObject(new Sql\DropTable('author'));		
		// создаём мультизапрос
        $multiQuery = $query1.";".$query2;      
        $adapter->query($multiQuery,'execute');
        */
		
		$adapter->execSqlObject(array(
			new Sql\DropTable('book'), 
			new Sql\DropTable('author')
		));
		
        
		$create = new Sql\CreateTable('author');	
		$create
			->addColumn(new Sql\Column\Integer('id', false, null, ['autoincrement' => true, 'comment' => 'идентификатор автора']))
			->addColumn(new Sql\Column\Varchar('name', 255))
			->addConstraint(new Sql\Constraint\PrimaryKey('id'));
		
		// пошаговый и быстрый способы выполнения sql-запроса
        //$result = $adapter->query($adapter->getSqlStringForSqlObject($create),'execute');
		$result = $adapter->execSqlObject($create);
						
    	return new ViewModel(
			array(
				'result' => $result,
			)
    	);   
    }

}