<?php
namespace ZendDb\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Db\Sql\Ddl;

/**
 *
 */
class DdlController extends AbstractActionController {

	/**
	 * route: /zenddb/index/index
	 */
    function indexAction() {
				
    	return new ViewModel(
    			array()
    	);      
    }
	
	/**
	 * Cоздание таблицы с помощью Zend\Db\Sql\Ddl\CreateTable
	 * route: /zenddb/create/index
	 */
    function createAction() {
		
		/*
		 * Получаем доступ к объекту sql-запросов Zend\Db\Sql\Sql через сервис-манагер
		 * Zend\Db\Sql\Sql необходим, чтобы выпонить запрос Zend\Db\Sql\Ddl\ с помощью его метода Sql::getSqlStringForSqlObject()
		 */
        $sql = $this->getServiceLocator()->get('Zend\Db\Sql\Sql');
		
		/*
		 * Перед созданием таблицы надо делать проверку на её существование и дропить её. 
		 * Для этого надо в конструкторе Ddl\CreateTable сделать дополнительный флаг CreateTable::DROP_IF_EXISTS)
		 * и сделать добавление команды: DROP TABLE IF EXISTS nametable 
		 */
		/*
		 * Принудительное удаление таблицы с помощью Ddl\DropTable. Работает некорректно.
		 * Надо изменить линию 24 на self::TABLE => 'DROP TABLE IF EXISTS %1$s'
		 */
		$query = $sql->getSqlStringForSqlObject(new Ddl\DropTable('book'));
		$sql->getAdapter()->query($query,'execute');
		
		/*** Варианты создания таблицы ***/
		// с помощью метода setTable()
		//$ddl = (new Ddl\CreateTable())->setTable('book');;
		// через конструктор
		$ddl = new Ddl\CreateTable('book');
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
		$ddl->addColumn(new Ddl\Column\Integer('id'));
		$ddl->addColumn(new Ddl\Column\Varchar('name', 255));
		
		// Выполнить Ddl-запрос
		$result = $sql->getAdapter()->query($sql->getSqlStringForSqlObject($ddl),'execute');
		
    	return new ViewModel(
    			array(
					'return' => $return,
				)
    	);      
    }
    		
}