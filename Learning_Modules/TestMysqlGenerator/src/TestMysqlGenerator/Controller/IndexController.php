<?php
namespace TestMysqlGenerator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use MysqlGenerator\Sql\Ddl;

/**
 *
 */
class IndexController extends AbstractActionController {

	/**
	 * router "/testmysqlgenerator/index/index"
	 */
    function indexAction() {
        
        $adapter = $this->getServiceLocator()->get('MysqlGenerator\Adapter\Adapter');
		
		$sql = new \MysqlGenerator\Sql\Sql($adapter);
        //$sql = $this->getServiceLocator()->get('Zend\Db\Sql\Sql');
		 
		 
		$multiQuery = 
			$sql->getSqlStringForSqlObject(new Ddl\DropTable('book')). ";" .
			$sql->getSqlStringForSqlObject(new Ddl\DropTable('author'));  
        $sql->getAdapter()->query($multiQuery,'execute');
		
		$ddl = new Ddl\CreateTable('author');
		$ddl->addColumn(new Ddl\Column\Integer('id', false, null, ['autoincrement' => true, 'comment' => 'идентификатор автора']));
		$ddl->addColumn(new Ddl\Column\Varchar('name', 255));
        $ddl->addConstraint(new Ddl\Constraint\PrimaryKey('id'));
        	
		// Выполнить Ddl-запрос
		$result = $sql->getAdapter()->query($sql->getSqlStringForSqlObject($ddl),'execute');
		
        // Таблица 'book', связанная с таблицей 'author' внешним ключом
        $ddl = new Ddl\CreateTable('book');     
        $ddl->addColumn(new Ddl\Column\Integer('id', false, null, ['autoincrement' => true, 'comment' => 'идентификатор книги']));
        $ddl->addColumn(new Ddl\Column\Integer('author_id'));
		$ddl->addColumn(new Ddl\Column\Varchar('name', 255));        
        $ddl->addConstraint(new Ddl\Constraint\PrimaryKey('id'));
        $ddl->addConstraint(new Ddl\Constraint\UniqueKey('name'));
        $ddl->addConstraint(new Ddl\Constraint\ForeignKey('id_author', 'author_id', 'author', 'id', 'CASCADE', 'CASCADE'));
        
        // Выполнить Ddl-запрос
		$result = $sql->getAdapter()->query($sql->getSqlStringForSqlObject($ddl),'execute');
			
		
    	return new ViewModel(
			array(
				'result' => $result,
			)
    	);   
    }

}