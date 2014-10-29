<?php
namespace MysqlGenerator;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements
	AutoloaderProviderInterface,
	ConfigProviderInterface
{
	/**
	 * AutoloaderProviderInterface
	 */
	function getAutoloaderConfig() {
		return array(
			'Zend\Loader\ClassMapAutoloader' => array(
				__DIR__ . '/autoload_classmap.php',
			),
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ ,
				)
			)
		);
	}

	/**
	 *  ConfigProviderInterface
	 */
	function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}

}