<?php
/*
 * !!! Количество ошибок и недоработок Zend/Db запредельное и использовать его не рекомендуется (его можно вообще удалить из фреймворка).
 * Для крупных(сложных) проектов рекомендуется использовать Doctrine2.
 * Для средних по сложности и высоконагруженных проектов лучше написать свой модуль работы с Бд
 * под определённый язык бд (напр MyQL), потому что смена языка бд проекта - редкое явление, а вот
 * дёргать при каждом запросе классы-адаптеры для адаптации языка драйвера SQL - неоправданная трата 
 * ресурсов сервера ради гипотетической кроссплатформенности проекта.
 * 
 * Класс Zend\Db\Adapter\Adapter - это обёртка php-драйверов работы с бд.
 * Он отвечает за соединение с бд с помощью указанного драйвера и выполнение запросов (прямых или подготовленных).
 * Это расширенный аналог PDO с возможностью работы с другими неPDO драйверами бд (оболочка или адаптер драйверов)
 * 
 * function __construct($driver, PlatformInterface $platform = null, ResultSet $queryResultSetPrototype = null)
 *      $driver - массив данных соединения с бд, определённый используемым драйвером бд или объект Zend\Db\Adapter\Driver\DriverInterface:
 *          driver	    required				Mysqli, Sqlsrv, Pdo_Sqlite, Pdo_Mysql, Pdo=OtherPdoDriver
 *          database	generally required		the name of the database (schema)
 *          username	generally required		the connection username
 *          password	generally required		the connection password
 *          hostname	not generally required	the IP address or hostname to connect to
 *          port		not generally required	the port to connect to (if applicable)
 *          charset		not generally required	the character set to use
 *      $platform - экземпляр Zend\Db\Platform\PlatformInterface, по-умолчанию создается на основе драйвера
 *      $queryResultSetPrototype - экземпляр Zend\Db\ResultSet\ResultSet
 * 
 * Как правило, массив соединения $driver размещают в глобальном конфиге: global.php и local.php(логин и пароль) или в Application-модуле.
 * Передача настроек соединения производится с помощью фабрики Zend\Db\Adapter\AdapterServiceFactory через регистрацию сервиса в сервис-менеджере (как правило в global.php)
 * Если необходимо работать с разными базами в разных модулях, то параметры настроек соединеия прописывают в конфиге модуля (getConfig())??? - Протестировать 
 * 
 * !!! Можно Zend\Db\Adapter\Adapter вообще выкинуть и заменить его простым PDO (или лёгкой обёрткой PDO), 
 * потому что использовать такую громоздкую обёртку ради поддержки устаревших драйверов(Mysqli, Sqlsrv) не имеет смысла, 
 * PDO само поддерживает все сервера.
 * Sql/Platform - это декоратор драйверов, а если оставить только поддержку PDO, то её можно выкинуть
 * Скорее лучше сделать отдельный Zend_Db_PDO_MySQL, потому что Zend_Db сильно завязано на применении разных платформ
 */
/*
 * config/global.php
 */
return array(
	//Параметры соединения с бд для Zend\Db\Adapter\Adapter
	'db' => array(
		'driver' => 'pdo_mysql',
		'hostname' => 'localhost',
		'database' => 'test',		
        'charset' => 'utf8',
    ),
	// Создание объекта соединения Zend\Db\Adapter\Adapter через фабрику с переданными параметрами соединения (как првило в global.php - см.выше и в local.php)
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
);
/*
 * config/local.php
 */
return array(
	'db' => array(
        'username' => 'root',
        'password' => '',
    ),
);
/*
 * AdapterServiceFactory создаёт объект Zend\Db\Adapter\Adapter и передаёт в его конструктор конфиги 'db':
 *	function createService(ServiceLocatorInterface $serviceLocator) {
 *		$config = $serviceLocator->get('Config');
 *		return new Adapter($config['db']);
 *	}
 */
// Самый простой способ соединения с бд
$adapter = new Zend\Db\Adapter\Adapter(array(
    'driver'   => 'pdo',
	'hostname' => 'localhost',
    'database' => 'zend_db_example',
    'username' => 'root',
    'password' => '',
	'charset'  => 'utf8'
 ));

/*
 * Для выполнение запросов напрямую или через подготовленный запрос используется метод:
 * query($sql, $parametersOrQueryMode = self::QUERY_MODE_PREPARE, ResultSet\ResultSetInterface $resultPrototype = null)
 *   $sql - строка sql-запроса
 *   $parametersOrQueryMode - режим запроса или массив переменных для полготовленного запроса (или объект контейнера Zend\Db\Adapter\ParameterContainer - класс-массив)
 *       Adapter::QUERY_MODE_PREPARE(по умолчанию) выполняет подготовленный запрос: Adapter::createStatement($sql, $parametersOrQueryMode)
 *       Adapter::QUERY_MODE_EXECUTE - выполняет запрос напрямую как PDO::exec($sql)
 * Возвращается объект Zend\Db\Adapter\Driver\Pdo\Result, если не было выборки Select
 * Возвращается объект Zend\Db\ResultSet\ResultSet после запроса выборки Select
 */
// прямой запрос
$adapter->query('CREATE DATABASE test CHARACTER SET utf8 COLLATE utf8_general_ci', Adapter::QUERY_MODE_EXECUTE);
// подготовленное выражение
$adapter->query('SELECT * FROM `artist` WHERE `id` = ?', array(5));

/*
 * Другой (прямой и более быстрый) способ создания подготовленный запроса через метод:
 * createStatement($initialSql = null, $initialParameters = null)
 *  $initialSql - подготовленное выражение
 *  $initialParameters - массив или объект контейнера Zend\Db\Adapter\ParameterContainer - класс-массив
 * Возвращает объект Driver\StatementInterface, методами которого запускается и обрабатывается результат запроса
 */
$statement = $adapter->createStatement('SELECT * FROM `artist` WHERE `id` = ?', array(5)); // тестить вид массива $optionalParameters
$result = $statement->execute();

/*
 * При создании сервиса таблицы с помощью фабрики, напр AlbumTableGateway, в неё можно передавать адаптер
 * двумя способами:
 *	1. через сервис-манагер: get('Zend\Db\Adapter\Adapter') в клозуре при регистрации фабрики в сервис-манагере: 
 */	
'AlbumTableGateway' => function ($sm) {
	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
	$resultSetPrototype = new ResultSet();
	$resultSetPrototype->setArrayObjectPrototype(new Album());
	return new TableGateway('album', $dbAdapter, null, $resultSetPrototype);
}
// или в классе-фабрике:
use Zend\ServiceManager\FactoryInterface;
class AlbumTableGatewayFactory implements FactoryInterface{
	createService( ServiceLocatorInterface $serviceLocator ) {
		$dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');
		$resultSetPrototype = new ResultSet();
		$resultSetPrototype->setArrayObjectPrototype(new Album());
		return new TableGateway('album', $this->adapter, null, $resultSetPrototype);
	}
}	
/*
 * 2.??? через автоматическую передачу адаптера в класс-фабрику с помощью AdapterAwareInterface-AdapterAwareTrait - тестировать!!!:
 * Не работает!!! По-видимому не прописано соответствующее событие. Протесчено в модуле ZendDb на сервисе 'Zend\Db\Sql\Sql'
 * trait AdapterAwareTrait {
 *		protected $adapter = null;
 *		function setDbAdapter(Adapter $adapter){
 *			$this->adapter = $adapter;
 *			return $this;
 *		}
 *	}
 */		
use Zend\ServiceManager\FactoryInterface;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Adapter\AdapterAwareTrait;
class AlbumTableGatewayFactory implements FactoryInterface, AdapterAwareInterface {
	use AdapterAwareTrait;
	createService( ServiceLocatorInterface $serviceLocator ) {
		$resultSetPrototype = new ResultSet();
		$resultSetPrototype->setArrayObjectPrototype(new Album());
		return new TableGateway('album', $this->adapter, null, $resultSetPrototype);
	}
}
/*
 * Любой класс, использующий адаптер вне конструтора, может внедрять адаптер в своё свойство с помощью AdapterAwareInterface-AdapterAwareTrait.
 * А можно ещё дальше пойти: создать свой класс MyTableGateway extends TableGateway implements  AdapterAwareInterface,
 * трейтить в него AdapterAwareTrait с адаптером. Проблема: конструктор TableGateway использует адаптер, а
 * AdapterAwareInterface внедряет адаптер после создания объекта(конструктора) - надо из конструтора вынести ипользование адаптера!!! 
 */