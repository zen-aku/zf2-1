<?php

namespace ZendDb\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Db\Sql\Ddl;

/**
 * Примеры Ddl-запросов с помощью классов Zend\Db\Sql\Ddl\
 */
class DdlController extends AbstractActionController {

	/**
	 * route: /zenddb/ddl/create/index
	 */
    function indexAction() {
				
    	return new ViewModel(
    			array()
    	);      
    }
	
	/**
	 * Cоздание таблицы с помощью Zend\Db\Sql\Ddl\CreateTable
	 * route: /zenddb/ddl/create
	 */
    function createAction() {	
		/*
		 * Получаем доступ к объекту sql-запросов Zend\Db\Sql\Sql через сервис-манагер
		 * Zend\Db\Sql\Sql необходим, чтобы выпонить запрос Zend\Db\Sql\Ddl\ с помощью его метода Sql::getSqlStringForSqlObject()
		 */
        $sql = $this->getServiceLocator()->get('Zend\Db\Sql\Sql');	
		/*
		 * Перед созданием таблицы надо делать проверку на её существование и дропить её. 
		 * Для этого надо в конструкторе Ddl\CreateTable сделать дополнительный флаг CreateTable::DROP_IF_EXISTS
		 * и сделать добавление команды: DROP TABLE IF EXISTS nametable 
		 */
		/*
		 * Принудительное удаление таблицы с помощью Ddl\DropTable. Работает некорректно.
		 * Надо изменить линию 24 на self::TABLE => 'DROP TABLE IF EXISTS %1$s'
		 */
        $query1 = $sql->getSqlStringForSqlObject(new Ddl\DropTable('book'));
		$query2 = $sql->getSqlStringForSqlObject(new Ddl\DropTable('author'));  
        // создаём мультизапрос
        $multiQuery = $query1.";".$query2; 
        $sql->getAdapter()->query($multiQuery,'execute');
       	
		/*** Варианты создания таблицы ***/
		// с помощью метода setTable()
		//$ddl = (new Ddl\CreateTable())->setTable('book');;
		// через конструктор
		$ddl = new Ddl\CreateTable('author');
		// создать temporary table
		//$ddl = new Ddl\CreateTable('book', true);
		
		/*
		 * Добавить колонки (классы в директории Zend\Db\Sql\Ddl\Column):
		 *  Blob	$name, $length, $nullable = false, $default = null, array $options = array()
		 *	Boolean	$name
		 *	Char	$name, $length
		 *	Column (generic)	$name = null
		 *	Date	$name
		 *	Decimal	$name, $precision, $scale = null
		 *	Float	$name, $digits, $decimal
		 *	Integer	$name, $nullable = false, $default = null, array $options = array()
		 *	Time	$name
		 *	Varchar	$name, $length
		 */
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
        // __construct($name, $column, $referenceTable, $referenceColumn, $onDeleteRule = null, $onUpdateRule = null)
        $ddl->addConstraint(new Ddl\Constraint\ForeignKey('id_author', 'author_id', 'author', 'id', 'CASCADE', 'CASCADE'));
        
        // Выполнить Ddl-запрос
		$result = $sql->getAdapter()->query($sql->getSqlStringForSqlObject($ddl),'execute');
               
    	return new ViewModel(
			array(
				'result' => $result,
			)
    	);      
    }
	
	/**
	 * Изменение таблицы с помощью Zend\Db\Sql\Ddl\AlterTable
	 * Для проведения тестов запустить предварительно createAction 
	 * route: /zenddb/ddl/alter
	 */
    function alterAction() {
		/*
		 * Получаем доступ к объекту sql-запросов Zend\Db\Sql\Sql через сервис-манагер
		 * Zend\Db\Sql\Sql необходим, чтобы выпонить запрос Zend\Db\Sql\Ddl\ с помощью его метода Sql::getSqlStringForSqlObject()
		 */
        $sql = $this->getServiceLocator()->get('Zend\Db\Sql\Sql');	
		
		/*** Варианты обращения к таблице ***/
		//$ddl = new Ddl\AlterTable()->setTable('book') ;
		$ddl = new Ddl\AlterTable('book');
		// optionally, as a temporary 
		//$ddl = new Ddl\AlterTable('book');
	
		// Добавить новую колонку в таблицу
		$ddl->addColumn(new Ddl\Column\Varchar('address', 255));
		$ddl->addColumn(new Ddl\Column\Char('mail', 25));
		
		// Добавить Ограничение
		$ddl->addConstraint(new Ddl\Constraint\UniqueKey('mail'));
		
		// Изменить колонку в таблице
		$ddl->changeColumn('name', new Ddl\Column\Char('alias', 20));
			
		// Выполнить Ddl-запрос
		$result = $sql->getAdapter()->query($sql->getSqlStringForSqlObject($ddl),'execute');
		
		
		$ddl = new Ddl\AlterTable('book');
			
		// Удалить колонку
		$ddl->dropColumn('address');
		
		// Удалить Ограничение - не работает
		//$ddl->dropConstraint('id_name');
			
		// Выполнить Ddl-запрос
		$result = $sql->getAdapter()->query($sql->getSqlStringForSqlObject($ddl),'execute');
		
		return new ViewModel(
			array(
				'result' => $result,
			)
    	);
	}
    		
}