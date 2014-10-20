<?php
namespace ZendDb\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 */
class SqlController extends AbstractActionController {

	/**
	 * route: /zenddb/sql/index
	 */
    function indexAction() {     
        /*
		 * Получаем доступ к адаптеру подключения к бд Zend\Db\Adapter\Adapter через сервис-манагер
		 * Сервис 'Zend\Db\Adapter\Adapter' зарегистрирован в сервис-манагере в global.php
		 */
		$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        // создать напрямую объект sql-запросов
        $sql = new \Zend\Db\Sql\Sql($adapter);
        $sql = null;
        
        /*
         * Получаем доступ к объекту sql-запросов Zend\Db\Sql\Sql через сервис-манагер
         * Сервис 'Zend\Db\Sql\Sql' надо зарегистрировать как фабрику в сервис-манагере в глобальном конфиге или Application-модуле. 
         * Но для тестов он зарегистрирван в module.getServiceConfig.php данного модуля тремя способами (третий не работает).
         */
        $sql = $this->getServiceLocator()->get('Zend\Db\Sql\Sql');
        $sql = null;
       	
    	return new ViewModel(
            array()
    	); 
    }    
       
    /**
     * Выборки с помощью класса Zend\Db\Sql\Select
	 * route: /zenddb/sql/select
	 */
    function selectAction() {
        
        // Получаем доступ к объекту sql-запросов Zend\Db\Sql\Sql через сервис-манагер
        $sql = $this->getServiceLocator()->get('Zend\Db\Sql\Sql');
        /*
        class Select extends AbstractSql implements SqlInterface, PreparableSqlInterface {
            const JOIN_INNER = 'inner';
            const JOIN_OUTER = 'outer';
            const JOIN_LEFT = 'left';
            const JOIN_RIGHT = 'right';
            const SQL_STAR = '*';
            const ORDER_ASCENDING = 'ASC';
            const ORDER_DESCENDING = 'DESC';

            public $where; // @param Where $where

            function __construct($table = null);
            function from($table);
            function columns(array $columns, $prefixColumnsWithTable = true);
            function join($name, $on, $columns = self::SQL_STAR, $type = self::JOIN_INNER);
            function where($predicate, $combination = Predicate\PredicateSet::OP_AND);
            function group($group);
            function having($predicate, $combination = Predicate\PredicateSet::OP_AND);
            function order($order);
            function limit($limit);
            function offset($offset);
        }
        */
        /*
         * Ecли указали имя таблицы в объекте Select(в конструкторе или через setTable('Users'), 
         * то уже нельзя будет вызвать метод from() для изменения имени таблицы.
         * select() возвращает объект Zend\Db\Sql\Select для указанной таблицы
         */
        //$sql->setTable('Users');     
        //$select = $sql->select('Users');
        //$select = $sql->select()->from('Users');      
        $select = $sql->select();
        
        // from():
        // as a string:
        $select->from('users');
        // as an array to specify an alias: produces SELECT "users".* FROM "users" AS "u"
        //$select->from(array('u' => 'users'));
    
        // columns():
        // as array of names
        $select->columns(array('login', 'password'));
        // as an associative array with aliases as the keys: users 'login' AS 'log', 'password' AS 'passw'
        //$select->columns(array('log' => 'login', 'passw' => 'password'));
        
        
        // Сделать запрос, используя подготовленное выражение
        $statement = $sql->prepareStatementForSqlObject($select)->execute();
        $result = $statement->getResource()->fetchAll(\PDO::FETCH_ASSOC);;
        
        // Сделать запрос, используя прямой вызов ???
        //$selectString = $sql->getSqlStringForSqlObject($select);
        //$result = $sql->getAdapter()->query($selectString, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        
        return new ViewModel(
            array(
                'result' => $result,
            )
    	);
   
    }
    
    
    
    
    
    		
}