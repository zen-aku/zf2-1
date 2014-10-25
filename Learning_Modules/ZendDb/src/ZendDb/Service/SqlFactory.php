<?php
namespace ZendDb\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Sql\Sql;

/**
 * Сервис 'Zend\Db\Sql\Sql': возвращает объект sql-запросов Zend\Db\Sql\Sql с внедрённым в него адаптером Zend\Db\Adapter\Adapter
 */
class SqlFactory implements FactoryInterface {

	/**
	 * Вернуть объект sql-запросов Zend\Db\Sql\Sql с внедрённым в него адаптером Zend\Db\Adapter\Adapter
	 * @param  ServiceLocatorInterface $serviceLocator - ServiceManager
	 * @return Zend\Db\Sql\Sql
	 */
	function createService( ServiceLocatorInterface $serviceLocator ) {
		return new Sql($serviceLocator->get('Zend\Db\Adapter\Adapter'));
	}
}