<?php
namespace DbGenerator;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\EventManager\EventInterface;

/**
 *
 */
class Module implements
	AutoloaderProviderInterface,
	ConfigProviderInterface,
    InitProviderInterface,  
    BootstrapListenerInterface
{
	/**
     *  AutoloaderProviderInterface
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

    /**
	 * InitProviderInterface
     * @param ModuleManagerInterface $moduleManager
	 */
	function init( ModuleManagerInterface $moduleManager ) {

	}

	/**
	 * BootstrapListenerInterface
     * @param EventInterface $e
	 */
	function onBootstrap( EventInterface $e ) {

	}

}