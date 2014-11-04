<?php
namespace TestMysqlGenerator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use MysqlGenerator\Adapter\Adapter;
use MysqlGenerator\Sql;

/**
 * Создать бд 'test', связанные таблицы 'users' и 'address' и заполнить их,
 * используя модудь MysqlGenerator
 */
class CreateDbController extends AbstractActionController {

	/*
	 * route: /testmysqlgenerator/createdb
	 * Создать бд 'test', связанные таблицы 'users' и 'address' и заполнить их
	 * используя модудь MysqlGenerator (подключить его)
	 */
	function indexAction() {	
		// Прямое подключение к серверу бд без указания имени бд:
		$adapter = new Adapter(array(
			'hostname' => 'localhost',
			'username' => 'root',
			'password' => '',
		 ));
		// Создадим бд 'test'
		$sql = "DROP DATABASE IF EXISTS test";
		$adapter->query( $sql, 'execute');
		$sql = "CREATE DATABASE test CHARACTER SET utf8 COLLATE utf8_general_ci";
		$adapter->query( $sql, 'execute');
		
	
		// Подключаемся к бд через сервис в сервис-манагере (зарегистрировать)
		$adapter = $this->getServiceLocator()->get('MysqlGenerator\Adapter\Adapter');
		
		// Создать таблицу 'users'
		$users = new Sql\CreateTable('users');
		$users
			->addColumn(new Sql\Column\Integer('id', false, null, ['autoincrement' => true]))
			->addColumn(new Sql\Column\Varchar('name', 255))
			->addColumn(new Sql\Column\Integer('age', false, 0))
			->addConstraint(new Sql\Constraint\PrimaryKey('id'));	    
        $adapter->execSqlObject($users);
		
		/*
		 * Заполнить таблицу 'users' значениями двумя способами:
		 * двумерным массивом(списком) или одномерным массивом(построчно) в values()
		 */
		$insertUsers = new Sql\Insert('users');
		// Списком: INSERT INTO users VALUES(null, 'John', 15),(null, 'Mike', 20),(null, 'Mary', 25);
		$insertUsers->values(array(
			[null, 'John', 15],
			[null, 'Mike', 20],
			[null, 'Mary', 25],
		))
		// Построчно: INSERT INTO users VALUES(null, 'Kate', 30)	
			->values([null, 'Kate', 30])
		// Построчно: INSERT INTO users VALUES(null, 'Alex', 35)		
			->values([null, 'Alex', 35])	
		;	
		$adapter->execSqlObject($insertUsers);
        
        
       /*
        *  INSERT INTO `users` (`name`, `age`) VALUES ('Nikole', '17')
        *  через Insert::set()
        */
       $insertUsers = new Sql\Insert('users');
       $insertUsers->set([
           'name' => 'Nikole',
           'age' => 17,
       ]);    
       //$adapter->execSqlObject($insertUsers); 
       
       /*
        *  INSERT INTO `users` (`name`, `age`) VALUES ('Nick', '19')
        *  через Insert::values()
        */
       $insertUsers = new Sql\Insert('users');
       $insertUsers->values([
           'name' => 'Nick',
           'age' => 19,
       ]);     
       //$adapter->execSqlObject($insertUsers); 
       
         
        /*
		 * INSERT INTO table SELECT 
		 */     
        // SELECT `users`.`name` AS `name`, `users`.`age` AS `age` FROM `users` WHERE id = 3
        $select = new Sql\Select('users');
        $select->columns(['name', 'age']);
        $select->where('id = 3');
        // INSERT INTO `users` (`name`, `age`) SELECT `users`.`name` AS `name`, `users`.`age` AS `age` FROM `users` WHERE id = 3
        $insertUsers = new Sql\Insert('users');
        $insertUsers->columns(['name', 'age']);
        $insertUsers->select($select);       
        //$adapter->execSqlObject($insertUsers);
        
                    
        /*
         * INSERT INTO `users` (`name`, `age`) VALUES 
         *      (SELECT `users2`.`name` AS `name` FROM `users` WHERE id = 5, '33'), 
         *      (SELECT `users2`.`name` AS `name` FROM `users` WHERE id = 4, '44')
         * Создать таблицу 'users2'
         */
        // Создать таблицу 'users2'
		$users2 = new Sql\CreateTable('users2');
		$users2
			->addColumn(new Sql\Column\Integer('id', false, null, ['autoincrement' => true]))
			->addColumn(new Sql\Column\Varchar('name', 255))
			->addColumn(new Sql\Column\Integer('age', false, 0))
			->addConstraint(new Sql\Constraint\PrimaryKey('id'));	       
		// Заполнить таблицу 'users' значениями
		$insertUsers2 = new Sql\Insert('users2');
		$insertUsers2->values(array(
			[null, 'John', 15],
			[null, 'Mike', 20],
		));
        $adapter->execSqlObject([$users2, $insertUsers2]);
         
        $selectName1 = new Sql\Select('users2');
        $selectName1->columns(['name'])->where('id = 1');
        $selectName2 = new Sql\Select('users2');
        $selectName2->columns(['name'])->where('id = 2');
        
        $insertUsers = new Sql\Insert('users');
        $insertUsers->columns(['name', 'age'])->values([
            [$selectName1, 33],
            [$selectName2, 44],
        ]);
        //$adapter->execSqlObject($insertUsers);
		
		
		
        /*
        echo '<pre>';
        echo $insertUsers->getSqlString($adapter);
		exit;
        */
       
		/*
		$sql = "
			CREATE TABLE users (
				id           INT         NOT NULL AUTO_INCREMENT,
				login        VARCHAR(50) NOT NULL DEFAULT '',
				password     VARCHAR(50) NOT NULL DEFAULT '',
				CONSTRAINT pk_users_id   PRIMARY KEY (id)                 
			) ENGINE = INNODB
		";
		$adapter->query( $sql, Adapter::QUERY_MODE_EXECUTE);
		
		$sql = "
			INSERT INTO users(login, password) VALUES 
				('Иванов', '12aff3'),
				('Петров', '123sf21'),
				('Сидоров', 'sfssf'),
				('Степанов', 'wrfsf'),
				('Никитин', 'sfsfss')
		";
		$adapter->query( $sql, Adapter::QUERY_MODE_EXECUTE);
		
		$sql = "
			CREATE TABLE page (
				id          INT         NOT NULL AUTO_INCREMENT,
				id_users	INT			NOT NULL DEFAULT 0,
				name        VARCHAR(50) NOT NULL DEFAULT '',
				CONSTRAINT pk_users_id  PRIMARY KEY (id),
				FOREIGN KEY (id_users) REFERENCES users(id) ON DELETE CASCADE
			) ENGINE = INNODB
		";
		$adapter->query( $sql, Adapter::QUERY_MODE_EXECUTE);
		
		$sql = "
			INSERT INTO page (name, id_users) VALUES 
				('Иванов блог', 1),
				('Иванов страница', 1),
				('Петров блог', 2),
				('Сидоров блог', 3),
				('Сидоров страница', 3),
				('Степанов блог', 4),
				('Никитин блог', 5)
		";
		$adapter->query( $sql, Adapter::QUERY_MODE_EXECUTE);
				
		// удалим соединение с бд
		$adapter = null;
		*/
		return new ViewModel(
			array(
				'result' => $result
			)
    	);
		
	}
	
}