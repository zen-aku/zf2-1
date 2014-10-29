<?php
namespace ZendDb\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Query\Ddl\Ddl;

/**
 * Сервис 'Zend\Db\Query\Ddl\Ddl': возвращает объект Ddl-запросов Zend\Db\Query\Ddl\Ddl с внедрённым в него адаптером Zend\Db\Adapter\Adapter
 */
class SqlFactory implements FactoryInterface {

	/**
	 * Вернуть объект Ddl-запросов Zend\Db\Query\Ddl\Ddl с внедрённым в него адаптером Zend\Db\Adapter\Adapter
	 * @param  ServiceLocatorInterface $serviceLocator - ServiceManager
	 * @return Zend\Db\Query\Ddl\Ddl
	 */
	function createService( ServiceLocatorInterface $serviceLocator ) {
		return new Ddl($serviceLocator->get('Zend\Db\Adapter\Adapter'));
	}
}