<?php
namespace TestMysqlGenerator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use MysqlGenerator\Sql\Ddl;
/**
 *
 */
class DdlController extends AbstractActionController {

	/**
	 * router "/testmysqlgenerator/sql/index"
	 */
    function indexAction() {
        
        $adapter = $this->getServiceLocator()->get('MysqlGenerator\Adapter\Adapter');
		$sql = new \MysqlGenerator\Sql\Sql($adapter);
        //$sql = $this->getServiceLocator()->get('Zend\Db\Sql\Sql');
		 
		$query1 = $sql->getSqlStringForSqlObject(new Ddl\DropTable('book'));
		$query2 = $sql->getSqlStringForSqlObject(new Ddl\DropTable('author'));  
		// создаём мультизапрос
        $multiQuery = $query1.";".$query2; 
        $sql->getAdapter()->query($multiQuery,'execute');	
		
		$ddl = new Ddl\CreateTable('author');		
		$ddl->addColumn(new Ddl\Column\Integer('id', false, null, ['autoincrement' => true, 'comment' => 'идентификатор автора']));
		$ddl->addColumn(new Ddl\Column\Varchar('name', 255));
        $ddl->addConstraint(new Ddl\Constraint\PrimaryKey('id'));
		$result = $sql->getAdapter()->query($sql->getSqlStringForSqlObject($ddl),'execute');
		
		
		
    	return new ViewModel(
			array(
				'result' => $result,
			)
    	);   
    }

}