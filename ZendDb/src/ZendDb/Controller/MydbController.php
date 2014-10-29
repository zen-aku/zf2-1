<?php
namespace ZendDb\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Db\Query\Ddl\Column;
use Zend\Db\Query\Ddl\Constraint;

/**
 * Примеры использования Ddl-запросов пользовательского(моего) модуля Zend_Db(https://github.com/algerd/Zend_Db)
 * Он был недоделан из-за огромного количества ошибок Zend\Db, на основе которого был сделан мой Zend_Db.
 */
class MydbController extends AbstractActionController {

	/**
	 * route: /zenddb/mydb/index
	 */
    function indexAction() {
		
        /*
		 * Получаем доступ к объекту Ddl-запросов Zend\Db\Query\Ddl через сервис-манагер
		 */
        $ddl = $this->getServiceLocator()->get('Zend\Db\Query\Ddl');
        
        $ddl = null;
        
    	return new ViewModel(
            array()
    	);      
    }
    
    /**
	 * route: /zenddb/mydb/drop
	 */
    function dropAction() {
		
        /*
		 * Получаем доступ к объекту Ddl-запросов Zend\Db\Query\Ddl через сервис-манагер
		 */
        $ddl = $this->getServiceLocator()->get('Zend\Db\Query\Ddl');
        
        /*
         * Ecли указали имя таблицы в объекте Select(в конструкторе или через setTable('Users')), 
         * то уже нельзя будет передавать имя таблицы через метод-команду
         */
        //$ddl->setTable('Book'); 
        //$drop = $ddl->dropTable();    
        //$drop = $ddl->dropTable('Book');
        $drop = $ddl->dropTable()->setTable('Book');              
        
        // Сделать запрос, используя прямой вызов
        $result = $ddl->getAdapter()->query($ddl->getSqlStringForSqlObject($drop), 'execute');
        $ddl = null;
        
    	return new ViewModel(
            array(
                'result' => $result,
            )
    	);      
    }
    
    /**
	 * route: /zenddb/mydb/create
	 */
    function createAction() {
		
        /*
		 * Получаем доступ к объекту Ddl-запросов Zend\Db\Query\Ddl через сервис-манагер
		 */
        $ddl = $this->getServiceLocator()->get('Zend\Db\Query\Ddl');
        
        /*
         * Ecли указали имя таблицы в объекте Select(в конструкторе или через setTable('Users')), 
         * то уже нельзя будет передавать имя таблицы через метод-команду
         */
        //$ddl->setTable('Book'); 
        //$create = $ddl->createTable();    
        $create = $ddl->createTable('Book');
        //$create = $ddl->createTable()->setTable('Book'); 
		
		//$create->setTemporary(true);
        
        $create->addColumn(new Column\Integer('id', false, null, ['autoincrement' => true, 'comment' => 'идентификатор автора']));
		$create->addColumn(new Column\Varchar('name', 255));
        
        // создание колонок через посредника без создания объектов через new (колонка сразу добавляется и возвращается для редактирования)
        $create->addColumn()->integer('art', true, 1, ['comment' => 'артикль']); //$create->addColumn(new Integer) and return Integer
        $create->addColumn()->varchar()		// $create->addColumn(new Varchar) and return Varchar
            ->setName('address')
            ->setLength(20);
        
        
        $create->addConstraint(new Constraint\PrimaryKey('id'));
        
        // Сделать запрос, используя прямой вызов
        $result = $ddl->getAdapter()->query($ddl->getSqlStringForSqlObject($create), 'execute');
        $ddl = null;
        
    	return new ViewModel(
            array(
                'result' => $result,
            )
    	);      
    }
    
    /**
	 * route: /zenddb/mydb/alter
	 */
    function alterAction() {
		
        /*
		 * Получаем доступ к объекту Ddl-запросов Zend\Db\Query\Ddl через сервис-манагер
		 */
        $ddl = $this->getServiceLocator()->get('Zend\Db\Query\Ddl');
        
        /*
         * Ecли указали имя таблицы в объекте Select(в конструкторе или через setTable('Users')), 
         * то уже нельзя будет передавать имя таблицы через метод-команду
         */
        //$ddl->setTable('Book'); 
        //$alter = $alter->alterTable();    
        $alter = $ddl->alterTable('Book');
        //$alter = $ddl->alterTable()->setTable('Book');              
        
		// Добавить новую колонку в таблицу
		$alter->addColumn(new Column\Varchar('address', 255));
		$alter->addColumn(new Column\Char('mail', 25));
		
		// Добавить Ограничение
		$alter->addConstraint(new Constraint\UniqueKey('mail'));
		
		// Изменить колонку в таблице
		$alter->changeColumn('name', new Column\Char('alias', 20));
			
		// Выполнить Ddl-запрос
		$result = $ddl->getAdapter()->query($ddl->getSqlStringForSqlObject($alter),'execute');
		
		
		$alter = $ddl->alterTable()->setTable('Book');
			
		// Удалить колонку
		$alter->dropColumn('address');
		
		// Удалить Ограничение - не работает
		//$alter->dropConstraint('id_name');
			
        // Сделать запрос, используя прямой вызов
        $result = $ddl->getAdapter()->query($ddl->getSqlStringForSqlObject($alter), 'execute');
        $ddl = null;
        
    	return new ViewModel(
            array(
                'result' => $result,
            )
    	);      
    }
    
}