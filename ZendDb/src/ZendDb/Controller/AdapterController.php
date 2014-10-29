<?php
namespace ZendDb\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Db\Adapter\Adapter;

/**
 * Тестовый контроллер работы с Zend\Db\Adapter\Adapter
 */
class AdapterController extends AbstractActionController {

	/**
	 * route: /zenddb/adapter/index
	 */
    function indexAction() {
        
		// Прямое подключение к серверу бд
		$adapter = new Adapter(array(
			'driver' => 'pdo_mysql',
			'hostname' => 'localhost',
			'database' => 'test',
			'username' => 'root',
			'password' => '',
		 ));
        
        // Разрываем соединение с бд
        $adapter = null;
		
    	return new ViewModel(
    			array()
    	);      
    }
    
    /**
	 * route: /zenddb/adapter/query
	 */
    function queryAction() {		
		/*
		 * Получаем доступ к адаптеру подключения к бд Zend\Db\Adapter\Adapter через сервис-манагер
		 * Сервис 'Zend\Db\Adapter\Adapter' зарегистрирован в сервис-манагере в global.php
		 */
		$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        
        // Делаем вставку, используя прямой запрос
        $sql = "INSERT INTO users(login, password) VALUES ('Вицин', 'ffddfbdf')";
        $queryExec = $adapter->query($sql, Adapter::QUERY_MODE_EXECUTE);
        
        // Делаем вставку, используя полготовленный запрос
        $sql = "INSERT INTO users(login, password) VALUES (?, ?)";
        $queryStat = $adapter->query($sql, ['Белов', 'hcshg']);
        
        // Делаем выборку, используя полготовленный запрос
        $sql = "SELECT login, password FROM users WHERE id < :id";
        $querySel = $adapter->query($sql, [':id'=> 4]);     // возвращается объект Zend\Db\ResultSet\ResultSet
        
        // Делаем выборку, используя полготовленный запрос
        $statement = $adapter->createStatement($sql, [':id'=> 3])->execute();   
        $resultStat = $statement->getResource()->fetchAll(\PDO::FETCH_ASSOC);
          
        // Разрываем соединение с бд
        $adapter = null;
		
    	return new ViewModel(
            array(
                'queryExec' => $queryExec,
                'queryStat' => $queryStat,
                'querySel' => $querySel,
                'resultStat'=> $resultStat,
            )
    	);      
    }
    
   		
}