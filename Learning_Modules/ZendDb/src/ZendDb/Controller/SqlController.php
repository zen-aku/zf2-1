<?php
namespace ZendDb\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Db\Sql\Predicate\Predicate;

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
         * Ecли указали имя таблицы в объекте Select(в конструкторе или через setTable('Users')), 
         * то уже нельзя будет вызвать метод from() для изменения имени таблицы.
         * select() возвращает объект Zend\Db\Sql\Select для указанной таблицы
         */
        //$sql->setTable('Users');     
        //$select = $sql->select('Users');
        //$select = $sql->select()->from('Users');
        $select = $sql->select();
        
        /** from(): **/
        /* as a string: */
        //$select->from('users');
        /* as an array to specify an alias: produces SELECT "users".* FROM "users" AS "u" */
        //$select->from(array('u' => 'users'));
    
        /** columns(): **/
		//$select->from('users');
        /* as array of names */
        //$select->columns(array('login', 'password'));
        /* as an associative array with aliases as the keys: users 'login' AS 'log', 'password' AS 'passw' */
        //$select->columns(array('log' => 'login', 'passw' => 'password'));
        
		/** join(): **/		
		/*SELECT users.login, users.password, page.name FROM page
         *    INNER JOIN users ON page.id_users = users.id 
		 */ 
		/*$select->from('page')->columns(['name']);	
		$select->join(
			'users',					
			'page.id_users = users.id',
			['login', 'password'],	// (optional) список колонок таблицы 'users'
			$select::JOIN_RIGHT		// (optional), one of inner, outer, left, right
	    );*/
		//$select->from(['p' => 'page'])->join(['u' => 'users'], 'p.id_users = u.id');
        
		/** order(): **/
		//$select->from('users');
		//$select->order('id DESC'); // produces 'id' DESC
		//$select->order('id DESC')->order('login ASC, password DESC'); // produces 'id' DESC, 'name' ASC, 'age' DESC
		//$select->order(array('id ASC', 'login DESC')); // produces 'name' ASC, 'age' DESC
		
		/** limit() and offset(): **/
		//$select->from('users');
		//$select->limit(2); // always takes an integer/numeric количество строк
		//$select->offset(1); // similarly takes an integer/numeric смещение с какой строки после заданного offset выбирать limit (у нас с id=2 и 3) 
		
		/** where(), having(): **/
		$select->from('users');
		/* SELECT * FROM "users" WHERE id > 1  AND id < 3 - Такой способ неполностью фильтрует входные данные - использовать только в крайних случаях */
		//$select->where(array('id > 1', 'id < 3'));
		/* SELECT * FROM "users" WHERE id" IN (?, ?, ?) */
		//$select->where(['id' => [1, 2, 3]]); // "c2" IN (?, ?, ?)
		/* c помощью объекта Zend\Db\Sql\Predicate\Predicate - предоставляет большую гибкость
		  особенно при использовании с классами-командами в Zend\Db\Sql\Predicate см.ниже */
		$select->where( (new Predicate())
		 		->in('id', [1, 2, 3])
				->between('id', 2, 5)
				->like('login', 'Петр%')
				->orPredicate(new \Zend\Db\Sql\Predicate\Like('login', 'Иван%'))
		);
		/* с помощью классов-команд в Zend\Db\Sql\Predicate - аналогов методов Zend\Db\Sql\Predicate\Predicate 
		 Предоставляет неплохую гибкость, но существенно нагружает код*/
		/*$select->where( array(
			new \Zend\Db\Sql\Predicate\In('id', [1, 2, 3]),
			new \Zend\Db\Sql\Predicate\Between('id', 2, 5),
			new \Zend\Db\Sql\Predicate\Like('login', 'Петр%'),
			new \Zend\Db\Sql\Predicate\Operator('id', '>', 1 )
		));*/		
		
        /** group: **/
        // $select->group();
		
        // Сделать запрос, используя подготовленное выражение
        $statement = $sql->prepareStatementForSqlObject($select)->execute();
        $result = $statement->getResource()->fetchAll(\PDO::FETCH_ASSOC);
        
        // Сделать запрос, используя прямой вызов
        //$selectString = $sql->getSqlStringForSqlObject($select);
        //$result = $sql->getAdapter()->query($selectString, 'execute');
        
        return new ViewModel(
            array(
                'result' => $result,
            )
    	); 
    }
    
    /**
     * Вставка с помощью класса Zend\Db\Sql\Insert
	 * route: /zenddb/sql/insert
	 */
    function insertAction() {       
        // Получаем доступ к объекту sql-запросов Zend\Db\Sql\Sql через сервис-манагер
        $sql = $this->getServiceLocator()->get('Zend\Db\Sql\Sql');
        
        /** Задать имя таблицы **/
        //$sql->setTable('Users');     
        //$insert = $sql->insert('Users');
        $insert = $sql->insert()->into('Users');  
        
        /* INSERT INTO users (login, password) VALUES ('Котов', '1233455') 
         * работает только если сделать в файле Zend\Db\Sql\Inaert.php изменения:
         * сделать дополнительный флаг VALUES_СOLUMNS и добавить дополнительный блок
         * Но останется всё-равно проблема: нельзя в один запрс запихнуть несколько строк values():
         * INSERT INTO users (login, password) VALUES 
         *  ('Котов1', '1233455'), 
         *  ('Котов2', '1233455'),
         * Можно только в цикле вызывать каждый раз columns()-values() и передавать им значения колонок и вставки, что делает такой способ вставки неэффективным
         */
        //$insert->columns(array('login', 'password'));      
        //$insert->values(array('Котов', '1233455'), $insert::VALUES_COLUMNS);
        
        // INSERT INTO `Users` VALUES (null, 'Котов', '1233455')
        //$insert->values(array(null, 'Ежов', '12cfbcb5'));
            
        /* По умолчанию VALUES_SET:
         * Должен формировать строку:
         * INSERT INTO users SET login = 'Котов', password = '1233455'
         * но формирует всё-равно строку:
         * INSERT INTO users(login, password) VALUES ('Котов', '1233455')
         */
        $insert->values(array('login'=> 'Кротов','password'=> '89hihnkkn'));
        
        // флаг VALUES_MERGE позволяет отдельно вносить (через отдельные вызовы values()) значения столбцов строки в таблицу 
        //$insert->values(['login'=>'Дубов'])->values(['password'=>'ddxvxv'], $insert::VALUES_MERGE);
        
        
        // С вложенным запросом INSERT INTO SELECT - тестовый пример(не работате в данном контексте селекта)
        //$select = $sql->select()->from('Users')->where(['id' => 3]);
        //$insert->values($select);
        // или то же самое
        //$insert->select($select);
        
        // Сделать запрос, используя прямой вызов
        $insertString = $sql->getSqlStringForSqlObject($insert);
        $result = $sql->getAdapter()->query($insertString, 'execute');
        
        // Сделать запрос, используя подготовленное выражение
        //$statement = $sql->prepareStatementForSqlObject($insert)->execute();
        //$result = $statement->getResource();
        
        return new ViewModel(
            array(
                'result' => $result,
            )
    	);
    }
    
    /**
     * Обновление с помощью класса Zend\Db\Sql\Update
	 * route: /zenddb/sql/update
	 */
    function updateAction() {
        // Получаем доступ к объекту sql-запросов Zend\Db\Sql\Sql через сервис-манагер
        $sql = $this->getServiceLocator()->get('Zend\Db\Sql\Sql');
        
        /** Задать имя таблицы **/
        //$sql->setTable('Users');     
        //$insert = $sql->update('Users');
        $update = $sql->update()->table('Users');  
        
        /** set: **/
        // where работает как в Select (см. выше)
        $update->set(['login' => 'algerd'])->where(['id' => 1]);
        // ??? как работает с VALUES_MERGE:
        //$update->set(['login' => 'Чернов'], $update::VALUES_MERGE);
        
        // Сделать запрос, используя прямой вызов
        $updateString = $sql->getSqlStringForSqlObject($update);
        $result = $sql->getAdapter()->query($updateString, 'execute');
        
        return new ViewModel(
            array(
                'result' => $result,
            )
    	);
    }
        
    /**
     * Удаление с помощью класса Zend\Db\Sql\Delete
	 * route: /zenddb/sql/delete
	 */
    function deleteAction() {
        // Получаем доступ к объекту sql-запросов Zend\Db\Sql\Sql через сервис-манагер
        $sql = $this->getServiceLocator()->get('Zend\Db\Sql\Sql');
        
        /** Задать имя таблицы **/
        //$sql->setTable('Users');     
        //$insert = $sql->delete('Users');
        $delete = $sql->delete()->from('Users');
        
        // where работает как в Select (см. выше)
        $delete->where(
            (new Predicate())
            ->in('id', [1, 2, 3])
            ->between('id', 2, 5)
            ->like('login', 'Петр%')
            ->orPredicate(new \Zend\Db\Sql\Predicate\Like('login', 'Иван%'))
		);
        
        // Сделать запрос, используя прямой вызов
        $deleteString = $sql->getSqlStringForSqlObject($delete);
        $result = $sql->getAdapter()->query($deleteString, 'execute');
        
        return new ViewModel(
            array(
                'result' => $result,
            )
    	);
    }
        
}