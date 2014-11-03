<?php
namespace TestMysqlGenerator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use MysqlGenerator\Sql;

class SqlController extends AbstractActionController {

	/**
	 * router "/testmysqlgenerator/sql/index"
	 */
    function indexAction() {
        
        $adapter = $this->getServiceLocator()->get('MysqlGenerator\Adapter\Adapter');
        
		/**
		 * Два способа вызова SQL-команд:
		 *	- напрямую через создание объека соответствующего класса-команды SQL
		 *  - через посредника объект класса-контроллера \MysqlGenerator\Sql\Sql
		 *		Имя таблицы, заданное в конструкторе Sql или методом Sql::setTable($table) будет 
		 *		сохраняться на все sql-команды-классы, кроме ddl-команд: alterTable, dropTable, createTable 
		 */
		$sql = new Sql\Sql();
		//$sql = new Sql\Sql('users');
		//$sql = new Sql\Sql->setTable('users');
		
		//$select = new Sql\Select('users');
		//$select = $sql->select('users');  // если таблица не была задана в конструкторе Sql или через Sql::setTable()
        $select = $sql->select();
        $select->from('users');	 // если таблица не была задана в конструкторе Sql, или через Sql::setTable($table), или через Sql::select($table), или в конструкторе Select
        $select->where( (new Sql\Predicate\Predicate())
			->in('id', [1, 2, 3])
			->between('id', 2, 5)
			->like('login', 'Петр%')
			->orPredicate(new Sql\Predicate\Like('login', 'Иван%'))
		);
		
		// пошаговый и быстрый способы выполнения sql-запроса
		//$statement = $adapter->prepareStatementForSqlObject($select)->execute();		
		$statement = $adapter->execPrepareStatement($select);
		$result = $statement->getResource()->fetchAll(\PDO::FETCH_ASSOC);
        		
		
		/*
		 * Класс Insert крайне забагован:
		 *	- не работает вставка:
		 *		$insert->columns(array('login', 'password'));      
         *		$insert->values(array('Котов', '1233455'), $insert::VALUES_COLUMNS);
		 *  - не работают подготовленные выражения при прямой вставке без указания столбцов
		 *		$insert->values(array(null, 'Ежов', '12cfbcb5'));
		 */

			
    	return new ViewModel(
			array(
				'result' => $result,
			)
    	);   
    }

}