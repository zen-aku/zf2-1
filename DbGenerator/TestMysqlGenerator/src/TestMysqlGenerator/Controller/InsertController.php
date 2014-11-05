<?php
namespace TestMysqlGenerator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use MysqlGenerator\Adapter\Adapter;
use MysqlGenerator\Sql;

/**
 * Тест класса MysqlGenerator\Sql\Insert
 */
class InsertController extends AbstractActionController {
	
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
	 * router "/testmysqlgenerator/insert/index"
	 */
    function indexAction() {       		
    	return new ViewModel(
			array()
    	);	
    }
	
	/**
	 * Заполнить таблицу 'users' значениями без указания столбцов:
	 * Списком: INSERT INTO users VALUES(null, 'John', 15),(null, 'Mike', 20),(null, 'Mary', 25);
	 * Построчно: INSERT INTO users VALUES(null, 'Kate', 30);
	 * router "/testmysqlgenerator/insert/insertvalues"
	 */
    function insertvaluesAction() { 
		
		$insertUsers = new Sql\Insert('users');
		/*
		 *  Списком: двумерным числовым массивом [[a1, b1, ...], [a2, b2, ...], [a3, b3, ...]]
		 *  Число значений во внутренних массивах должно совпадать с числом столбцов таблицы !
		 *  INSERT INTO users VALUES(null, 'John', 15),(null, 'Mike', 20),(null, 'Mary', 25);
		 */
		$insertUsers->values(array(
			[null, 'John', 15],
			[null, 'Mike', 20],
			[null, 'Mary', 25],
		));
		/*
		 *  Построчно: одномерным числовым массивом [a, b, ...]
		 *  Если задавать построчно несколько раз, то одномерные массивы будут собираться в один двумерный массив.
		 *  Число значений в массиве должно совпадать с числом столбцов таблицы !
		 *  INSERT INTO users VALUES(null, 'Kate', 30);
		 */
		$insertUsers	
			->values([null, 'Kate', 30])		
			->values([null, 'Alex', 35]);	// INSERT INTO users VALUES(null, 'Kate', 30), (null, 'Alex', 35);
		
		// Прямое выполнение запроса:
		//$result = $this->adapter->execSqlObject($insertUsers);
		// Выполнение запроса через создание подготовленного выражения
        $result = $this->adapter->execPrepareStatement($insertUsers);
				
    	return new ViewModel(												
			array(
				'result' => $result
			)
    	);	
    }
	
	/**
	 * Задать столбцы и заполнить таблицу значениями:
	 * Списком: INSERT INTO users(name, age) VALUES('John', 15),('Mike', 20),('Mary', 25);
	 * Построчно: INSERT INTO users(name, age) VALUES('Kate', 30)	
	 * router "/testmysqlgenerator/insert/intocolumns"
	 */
    function intocolumnsAction() {  
		
        $insertUsers = new Sql\Insert('users');	
		/*
		 * Задать колонки.
		 * При этом все ранее занесённые в этот объект Insert значения (через values(), set() или select()) и колонки будут очищены.
		 * Поэтому надо следить за порядком добавления : сначала колонки, затем значения.
		 */
		$insertUsers->columns(['name', 'age']);
        /*
		 * Списком: двумерным числовым массивом [[a1, b1, ...], [a2, b2, ...], [a3, b3, ...]]
		 * ??? Число значений во внутренних массивах должно совпадать с числом задаваемых столбцов!
		 * ??? Число значений <= число задаваемых столбцов (пропущенные значения будут заполняться дефолтными из таблицы)
		 * INSERT INTO users(name, age) VALUES('John', 15),('Mike', 20),('Mary', 25);
		 */
        $insertUsers->values(array(
			['John', 15],
			['Mike', 20],
			['Mary', 25],
		));
		/*
		 * Построчно: одномерным числовым массивом [a, b, ...]
		 * Если задавать построчно несколько раз, то одномерные массивы будут собираться в один двумерный массив.
		 * ??? Число значений в массиве должно совпадать с числом столбцов таблицы !
		 * ??? Число значений <= число задаваемых столбцов (пропущенные значения будут заполняться дефолтными из таблицы)
		 * INSERT INTO users(name, age) VALUES('Kate', 30);
		 */	
		$insertUsers			
			->values(['Kate', 30])	
			->values(['Alex', 35]);		// INSERT INTO users(name, age) VALUES('Kate', 30), ('Alex', 35);
		
		// Прямое выполнение запроса:
		//$result = $this->adapter->execSqlObject($insertUsers);
		// Выполнение запроса через создание подготовленного выражения
        $result = $this->adapter->execPrepareStatement($insertUsers);
			
    	return new ViewModel(
			array(
				'result' => $result
			)
    	);	
    }
	
	/**
	 * Задать в выражении INSERT соответствия 'столбец'=>'значение':
	 * INSERT INTO tbl_name SET a=1, b=2, c=3
	 * Будет создан шаблон INSERT INTO tbl_name(a, b, c) SET (1, 2, 3)
	 * Надо учитывать, что каждый новый set() будет очищать все значения объекта Insert,
	 * ранее заданные через values(), set() или select(), и колонки, заданные через columns() 
	 * router "/testmysqlgenerator/insert/setvalues"
	 */
    function setvaluesAction() {  		
		/* 
		 * Insert::set($values) - так будет производительнее, чем через Insert::values($values)
		 * $values - только ассоциативный массив вида ['col1'=>val1, 'col2'=>val2, ...]
         * INSERT INTO `users` (`name`, `age`) VALUES ('Nikole', '17')
         */
       $insertUsers = new Sql\Insert('users');
       $insertUsers->set([
           'name' => 'Nikole',
           'age' => 17,
       ]);    
        // Прямое выполнение запроса:
		//$result = $this->adapter->execSqlObject($insertUsers);
		// Выполнение запроса через создание подготовленного выражения
		//$result = $this->adapter->execPrepareStatement($insertUsers);	
       
       /*
	    * То же самое через: Insert::values($values)
	    * $values в данном контексте это ассоциативный массив вида ['col1'=>val1, 'col2'=>val2, ...]
        * INSERT INTO `users` (`name`, `age`) VALUES ('Nick', '19')
        */
       $insertUsers = new Sql\Insert('users');
       $insertUsers->values([
           'name' => 'Nick',
           'age' => 19,
       ]);     
        // Прямое выполнение запроса:
		//$result = $this->adapter->execSqlObject($insertUsers);
		// Выполнение запроса через создание подготовленного выражения
		$result = $this->adapter->execPrepareStatement($insertUsers);	
		
    	return new ViewModel(
			array(
				'result' => $result
			)
    	);	
    }
		
	/** 
	 * Добавить значения из выражения SELECT:
	 * INSERT INTO table SELECT ...
	 * Надо учитывать, что каждый новый select() будет очищать все значения объекта Insert,
	 * ранее заданные через values(), set() или select()
	 * router "/testmysqlgenerator/insert/select"
	 */
    function selectAction() { 		
		// добавить значения в таблицу `users` для тестирования:
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
		$result = $this->adapter->execPrepareStatement($insertUsers);	
		
    	return new ViewModel(
			array(
				'result' => $result
			)
    	);	
    }
		
	/**
	 * Заполнить таблицу 'users' значениями-выборками SELECT в VALUES:
	 * INSERT INTO `users` (`name`, `age`) VALUES 
     *      ((SELECT `users2`.`name` AS `name` FROM `users` WHERE id = 1), '33'), 
     *      ((SELECT `users2`.`name` AS `name` FROM `users` WHERE id = 2), '44')
	 * Работает как обычный метод values(), в массив которого в качестве передаваемых значений передаются объекты SELECT
	 * router "/testmysqlgenerator/insert/valuesselect"
	 */
    function valuesselectAction() {  		 
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
		$result = $this->adapter->execSqlObject($insertUsers);
		// Выполнение запроса через создание подготовленного выражения
		//$result = $this->adapter->execPrepareStatement($insertUsers);	
		
    	return new ViewModel(
			array(
				'result' => $result
			)
    	);	
    }
	
	/**
	 * 
	 * router "/testmysqlgenerator/insert/expression"
	 */
    function expressionAction() {   
		
		// Прямое выполнение запроса:
		//$result = $this->adapter->execSqlObject($insertUsers);
		// Выполнение запроса через создание подготовленного выражения
		//$result = $this->adapter->execPrepareStatement($insertUsers);	
		
    	return new ViewModel(
			array(
				'result' => $result
			)
    	);	
    }
	

}