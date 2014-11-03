<?php
namespace TestMysqlGenerator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use MysqlGenerator\Sql;

/**
 * Быстрый старт
 */
class IndexController extends AbstractActionController {

	/**
	 * router "/testmysqlgenerator/index/index"
	 */
    function indexAction() {
        
        $adapter = $this->getServiceLocator()->get('MysqlGenerator\Adapter\Adapter');
		 
		// Удалить таблицы 'book' и 'author'
		$adapter->execSqlObject(array(
			new Sql\DropTable('book'), 
			new Sql\DropTable('author')
		));
		
		// Таблица 'author'
		$author = new Sql\CreateTable('author');
		$author
			->addColumn(new Sql\Column\Integer('id', false, null, ['autoincrement' => true, 'comment' => 'идентификатор автора']))
			->addColumn(new Sql\Column\Varchar('name', 255))
			->addConstraint(new Sql\Constraint\PrimaryKey('id'));
		
        // Таблица 'book', связанная с таблицей 'author' внешним ключом
        $book = new Sql\CreateTable('book');     
        $book
			->addColumn(new Sql\Column\Integer('id', false, null, ['autoincrement' => true, 'comment' => 'идентификатор книги']))
			->addColumn(new Sql\Column\Integer('author_id'))
			->addColumn(new Sql\Column\Varchar('name', 255))        
			->addConstraint(new Sql\Constraint\PrimaryKey('id'))
			->addConstraint(new Sql\Constraint\UniqueKey('name'))
			->addConstraint(new Sql\Constraint\ForeignKey('id_author', 'author_id', 'author', 'id', 'CASCADE', 'CASCADE'));
		
		// Выполнить мультизапрос создания двух таблиц 'author' и 'book'
		$result = $adapter->execSqlObject(array($author, $book));
		
    	return new ViewModel(
			array(
				'result' => $result,
			)
    	);
		
    }

}