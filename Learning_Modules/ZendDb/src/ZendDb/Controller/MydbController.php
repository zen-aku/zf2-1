<?php
namespace ZendDb\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Db\Query\Ddl\Column;
use Zend\Db\Query\Ddl\Constraint;

/**
 * Примеры использования Ddl-запросов пользовательского модуля Db
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
        //$drop = $ddl->createTable();    
        $drop = $ddl->createTable('Book');
        //$create = $ddl->createTable()->setTable('Book');  
        
        $create->addColumn(new Column\Integer('id', false, null, ['autoincrement' => true, 'comment' => 'идентификатор автора']));
		$create->addColumn(new Column\Varchar('name', 255));
        $create->addConstraint(new Constraint\PrimaryKey('id'));
        
        // Сделать запрос, используя прямой вызов
        $result = $ddl->getAdapter()->query($ddl->getSqlStringForSqlObject($create), 'execute');
        
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
        //$drop = $ddl->alterTable()->setTable('Book');              
        
        // Сделать запрос, используя прямой вызов
        $result = $ddl->getAdapter()->query($ddl->getSqlStringForSqlObject($alter), 'execute');
        
    	return new ViewModel(
            array(
                'result' => $result,
            )
    	);      
    }
    
}