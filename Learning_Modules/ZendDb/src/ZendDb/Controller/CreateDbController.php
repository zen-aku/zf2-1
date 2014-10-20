<?php
namespace ZendDb\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Db\Adapter\Adapter;

/**
 * Создать бд 'test', таблицу 'user' и заполнить её
 */
class CreateDbController extends AbstractActionController {

	/*
	 * route: /zenddb/createdb
	 * Создать бд 'test', таблицу 'user' и заполнить её
	 */
	function indexAction() {
		
		// Прямое подключение к серверу бд без указания имени бд:
		$adapter = new Adapter(array(
			'driver' => 'pdo_mysql',
			'hostname' => 'localhost',
			'database' => '',
			'username' => 'root',
			'password' => '',
		 ));
		// Создадим бд 'test'
		$sql = "DROP DATABASE IF EXISTS test";
		$adapter->query( $sql, Adapter::QUERY_MODE_EXECUTE);
		$sql = "CREATE DATABASE test CHARACTER SET utf8 COLLATE utf8_general_ci";
		$adapter->query( $sql, Adapter::QUERY_MODE_EXECUTE);
		
		// Подключаемся к бд через сервис в сервис-манагере, зарегистрированный в global.php
		$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
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
				
		// удалим соединение с бд
		$adapter = null;
		
		return new ViewModel(
    			array()
    	);
	}
	
}