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
		;
		
		// Заполнить таблицу 'users' значениями
		$insertUsers = new Sql\Insert('users');
		$insertUsers->values(array(
			[null, 'John', 15],
			[null, 'Mike', 20],
			[null, 'Mary', 25],
		))	
			->values([null, 'Kate', 30])
			->values([null, 'Alex', 35])	
		;
	
		$insertUsers->getValues();
		
		
		
		// Выполнить мультизапрос
		$result = $adapter->execSqlObject([
			$users,
			$insertUsers
		]);
		
		
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