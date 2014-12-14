<?php
/*
 * Pada skala aplikasi yang besar, kita sering menjumpai aplikasi yang menggunakan beberapa database sekaligus. Zend Framework 2 memfasilitasi kebutuhan ini dengan settingan yang super flexible. Cukup memanggil array konfigurasi, tak perlu pusing memikirkan sintax sql yang mungkin berbeda di teknologi database lain, cukup configure, and run!
 * Anggap saja, kita ingin membangun aplikasi dengan 2 database, yaitu Postgresql, dan Mysql, maka yang kita perlukan pertama adalah setting konfigurasi global di Zend Framework kita, seperti contoh berikut :
 * 
 * Пример конфигурирования двух разных бд разных sql-драйверов
 */

// ZendSkeletonApplication\config\autoload\global.php	
return array(
    'db-pgsql' => array(
        'driver' => 'pdo_pgsql',
        'dbname' => 'zf2',
        'hostname' => 'localhost',
        'username' => 'developer',
        'password' => '123456'
    ),     
    'db-mysql' => array(
        'driver' => 'pdo_mysql',
        'dbname' => 'zf2',
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => ''
    ),
);
 
// Application\Module.php
public function getServiceConfig() {
	
	return array(
		'factories' => array(
			'db-adapter-postgresql' =>  function($sm) {
				$config = $sm->get('config');
				$config = $config['db-pgsql'];
				$dbAdapter = new DbAdapter($config);
				return $dbAdapter;
			},
			'db-adapter-mysql' =>  function($sm) {
				$config = $sm->get('config');
				$config = $config['db-mysql'];
				$dbAdapter = new DbAdapter($config);
				return $dbAdapter;
			},
		),
					
	);
}

//<Module>\Module.php
public function getServiceConfig() {
	return array(
		'factories' => array(
			'sample-table-pg' =>  function($sm) {
				$dbAdapter = $sm->get('db-adapter-postgresql');
				$table = new SampleTable($dbAdapter);
				return $table;
			},
			'sample-table-my' =>  function($sm) {
				$dbAdapter = $sm->get('db-adapter-mysql');
				$table = new SampleTable($dbAdapter);
				return $table;
			},

		),
	);
}

// Controller
public function getSampleTable() {
	if (!$this->sampleTable) {
		$sm = $this->getServiceLocator();
		$this->sampleTable = $sm->get('sample-table-my'); 
		//change to "sample-table-pg" if need pgsql, just it <span class="wp-smiley wp-emoji wp-emoji-wink" title=";)">;)</span>
	}
	return $this->sampleTable;
}