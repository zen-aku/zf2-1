<?php
namespace TestMysqlGenerator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use MysqlGenerator\Adapter\Adapter;
use MysqlGenerator\Sql;

/**
 * Быстрый старт
 */
class IndexController extends AbstractActionController {
	
	/**
	 * @var Adapter
	 */
	protected $adapter = null;
	
	/**
	 * Создать бд 'test'
	 * Инициализировать $this->adapter и создать таблицы 'users' и 'users2'
	 */
	public function __construct() {
		$adapter = new Adapter(array('hostname'=>'localhost','username'=>'root','password'=>''));
		
		// Создадим бд 'test'
		$sql = "DROP DATABASE IF EXISTS test";
		$adapter->query( $sql, 'execute');
		$sql = "CREATE DATABASE test CHARACTER SET utf8 COLLATE utf8_general_ci";
		$adapter->query( $sql, 'execute');
		
		$this->adapter = new Adapter(array('hostname'=>'localhost', 'database' => 'test', 'username'=>'root','password'=>''));	
		
		// Создать таблицу 'users'
		$users = new Sql\CreateTable('users');
		$users
			->addColumn(new Sql\Column\Integer('id', false, null, ['autoincrement' => true]))
			->addColumn(new Sql\Column\Varchar('name', 255))
			->addColumn(new Sql\Column\Integer('age', false, 0))
			->addConstraint(new Sql\Constraint\PrimaryKey('id'));	    	
		// Создать таблицу 'users2'
		$users2 = new Sql\CreateTable('users2');
		$users2
			->addColumn(new Sql\Column\Integer('id', false, null, ['autoincrement' => true]))
			->addColumn(new Sql\Column\Varchar('name', 255))
			->addColumn(new Sql\Column\Integer('age', false, 0))
			->addConstraint(new Sql\Constraint\PrimaryKey('id'));	       
		$this->adapter->execSqlObject([$users, $users2]);
	}
	
	/**
	 * router "/testmysqlgenerator/index/index"
	 */
    function indexAction() {       		
    	return new ViewModel(
			array()
    	);	
    }
	
	/**
	 * router "/testmysqlgenerator/index/insert"
	 */
    function insertAction() {
        
		//////insertvaluesAction()
        $insertUsers = new Sql\Insert('users');
		$insertUsers->values(array(
			[null, 'John', 15],
			[null, 'Mike', 20],
			[null, 'Mary', 25],
		));
		$insertUsers	
			->values([null, 'Kate', 30])
			->values([null, 'Alex', 35]);
		// Прямое выполнение запроса:
		//$result = $this->adapter->execSqlObject($insertUsers);
		// Выполнение запроса через создание подготовленного выражения
		//$result = $this->adapter->execPrepareStatement($insertUsers);	
			 
		
		//////intocolumnsAction()
		$insertUsers = new Sql\Insert('users');
		$insertUsers->columns(['name', 'age']);
		//$insertUsers->addColumn('name')->addColumn('age');	
		$insertUsers->values(array(
			['John', 15],
			['Mike', 20],
			['Mary', 25],
		));
		$insertUsers			
			->values(['Kate', 30])	
			->values(['Alex', 35]);
		// Прямое выполнение запроса:
		//$result = $this->adapter->execSqlObject($insertUsers);
		// Выполнение запроса через создание подготовленного выражения
		//$result = $this->adapter->execPrepareStatement($insertUsers);	
			
		
		//////setvaluesAction()		
		$insertUsers->values([
			'name' => 'Nikole',
			'age' => 17,
		]); 
		// Прямое выполнение запроса:
		//$result = $this->adapter->execSqlObject($insertUsers);
		// Выполнение запроса через создание подготовленного выражения
		//$result = $this->adapter->execPrepareStatement($insertUsers);	
		
		
		////// Options
		//$insertUsers = new Sql\Insert('users', null, [Sql\Insert::IGNORE, Sql\Insert::HIGH_PRIORITY]);
		$insertUsers = new Sql\Insert('users');
		//$insertUsers->setOptions([Sql\Insert::IGNORE, Sql\Insert::HIGH_PRIORITY]);
		$insertUsers->ignore()->highPriority();		
		$insertUsers->values(['name' => 'Nikole','age' => 17,]);
		// Прямое выполнение запроса:
		//$result = $this->adapter->execSqlObject($insertUsers);
		// Выполнение запроса через создание подготовленного выражения
		//$result = $this->adapter->execPrepareStatement($insertUsers);	
		
		
		//////selectAction()
		$insert = new Sql\Insert('users');
		$insert->values(array(
			[null, 'John', 15],
			[null, 'Mike', 20],
			[null, 'Mary', 25],
			[null, 'Kate', 30],
			[null, 'Alex', 35],
		));
		$this->adapter->execPrepareStatement($insert);
		
		// SELECT `users`.`name` AS `name`, `users`.`age` AS `age` FROM `users` WHERE id = 3
        $select = new Sql\Select('users');
        $select->columns(['name', 'age']);
        $select->where('id = 3');		// ('Mary', 25)
		
        // INSERT INTO `users`(`name`, `age`) SELECT `users`.`name` AS `name`, `users`.`age` AS `age` FROM `users` WHERE id = 3
        $insertUsers = new Sql\Insert('users');
        $insertUsers->columns(['name', 'age']);
        $insertUsers->select($select);	// (6, 'Mary', 25)
			
		// Прямое выполнение запроса:
		//$result = $this->adapter->execSqlObject($insertUsers);
		// Выполнение запроса через создание подготовленного выражения
		//$result = $this->adapter->execPrepareStatement($insertUsers);	
		
		
		
		//////valuesselectAction() {  		 
		// добавить значения в таблицу 'users2' для тестирования
		$insertUsers2 = new Sql\Insert('users2');
		$insertUsers2->values(array(
			[null, 'John', 15],
			[null, 'Mike', 20],
		));   
        $this->adapter->execSqlObject($insertUsers2); 
		
		/*
         * INSERT INTO `users` (`name`, `age`) VALUES 
         *      ((SELECT `users2`.`name` AS `name` FROM `users` WHERE id = 1), '33'), 
         *      ((SELECT `users2`.`name` AS `name` FROM `users` WHERE id = 2), '44')
         */
		// SELECT
        $selectName1 = new Sql\Select('users2');
        $selectName1->columns(['name'])->where('id = 1');
        $selectName2 = new Sql\Select('users2');
        $selectName2->columns(['name'])->where('id = 2');
        // INSERT
        $insertUsers = new Sql\Insert('users');
        $insertUsers->columns(['name', 'age'])->values([
            ['Olya', 9],
            [$selectName1, 33],
            [$selectName2, 44],
        ]);
       	
		// Прямое выполнение запроса:
		//$result = $this->adapter->execSqlObject($insertUsers);
		// Выполнение запроса через создание подготовленного выражения
		$result = $this->adapter->execPrepareStatement($insertUsers);	
		
		
		////// Partition (тестово)
		/*
		CREATE TABLE users (
			id      INT         NOT NULL AUTO_INCREMENT,
			name    VARCHAR(50) NOT NULL DEFAULT '',
			age     INT			NOT NULL DEFAULT 0,
			CONSTRAINT pk_users_id   PRIMARY KEY (id)                 
		) ENGINE = INNODB
		PARTITION BY RANGE COLUMNS(age) (
			PARTITION age7 VALUES LESS THAN (7),
			PARTITION age18 VALUES LESS THAN (18),	
			PARTITION age40 VALUES LESS THAN (40),
		);
		*/
		$insertUsers = new Sql\Insert('users');
		$insertUsers->partitions(['age7']);
		//$insertUsers->addPartitions('age7');
		$insertUsers->columns(['name', 'age']);
		$insertUsers			
			->values(['Kate', 6])	
			->values(['Alex', 3]);
		
		// Тестово, потому что не созданы разделы в таблице 'users'
		//$result = $insertUsers->getSqlString($this->adapter);
		$result = $insertUsers->prepareStatement($this->adapter, new \MysqlGenerator\Adapter\StatementContainer());
		
    	return new ViewModel(
			array(
				'result' => $result,
			)
    	);	
    }
	
	/**
	 * router "/testmysqlgenerator/index/select"
	 */
    function selectAction() { 
		
		$insertUsers = new Sql\Insert('users');
		$insertUsers->values(array(
			[null, 'John', 15],
			[null, 'Mike', 20],
			[null, 'Mary', 25],
			[null, 'Kate', 30],
			[null, 'Alex', 35],		
		));
		$this->adapter->execSqlObject($insertUsers);
		
		
		/////
		// SELECT `u`.`id`, `u`.`age`, `u`.`name` AS `nm` FROM `test`.`users` AS `u`
		$select = new Sql\Select(['u' => 'users'], 'test');
		$select->columns(['id', 'a'=>'age', 'nm'=>'name']);
		$select->partition(['p1', 'p2']);
		
		$join = new Sql\Keyword\Join(['addr' => 'address'], 'test');
		$join
			->partition(['p0'])
			->type(['natural', 'outer', 'right', 'join'])
			//->type(['straight_join'])
			->on(['id' => 'users_id'])
			//->on('address.users_id = users.id or address.age = users.age')
			->columns(['city', 'country'])	
		;
		$select->join($join);
		
		
		
		
		
		$result = $select->getSqlString($this->adapter);
		//$result = $this->adapter->execSqlObject($select);
		
    	return new ViewModel(
			array(
				'result' => $result,
			)
    	);	
    }
	

}