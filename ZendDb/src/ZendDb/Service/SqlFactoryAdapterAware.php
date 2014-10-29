<?php
namespace ZendDb\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Adapter\AdapterAwareTrait;
use Zend\Db\Sql\Sql;

/**
 * Не работате AdapterAwareInterface!!! По-видимому не прописано соответствующее событие.
 * Сервис 'Zend\Db\Sql\Sql': возвращает объект sql-запросов Zend\Db\Sql\Sql с внедрённым в него адаптером Zend\Db\Adapter\Adapter
 * Вызов адаптера Zend\Db\Adapter\Adapter должен быть с помощью AdapterAwareInterface, но это не работает!!! Почему???
 */
class SqlFactoryAdapterAware implements FactoryInterface, AdapterAwareInterface {
    use AdapterAwareTrait;
	/**
	 * Вернуть объект sql-запросов Zend\Db\Sql\Sql с внедрённым в него адаптером Zend\Db\Adapter\Adapter
	 * @param  ServiceLocatorInterface $serviceLocator - ServiceManager
	 * @return Zend\Db\Sql\Sql
	 */
	function createService( ServiceLocatorInterface $serviceLocator ) {
		return new Sql($this->adapter);
	}
}