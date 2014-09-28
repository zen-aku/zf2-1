<?php
namespace Modulename;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;

/**
 * Абстрактный класс модуля, в котором представлены все основные методы-конфигураторы,
 * возвращаемые массивы из соответствующих инклудов из диретории __DIR__ /config/
 */
abstract class ModuleConfig implements
	AutoloaderProviderInterface,
	ConfigProviderInterface,
	ControllerProviderInterface,
	ServiceProviderInterface,
	ViewHelperProviderInterface,
    ControllerPluginProviderInterface
{
	// AutoloaderProviderInterface
	function getAutoloaderConfig() {
		return array(
			'Zend\Loader\ClassMapAutoloader' => array(
				__DIR__ . '/autoload_classmap.php',
			),
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
				)
			)
		);
	}

	// ConfigProviderInterface
	function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}

	//ControllerProviderInterface
	function getControllerConfig() {
		return include __DIR__ . '/config/module.controller_config.php';
	}

	// ServiceProviderInterface
	function getServiceConfig() {
		return include __DIR__ . '/config/module.service_config.php';
	}

	// ViewHelperProviderInterface
	function getViewHelperConfig() {
		return include __DIR__ . '/config/module.viewhelper_config.php';
	}
    
    // ControllerPluginProviderInterface
    function getControllerPluginConfig() {
        return include __DIR__ . '/config/module.plugin_config.php';
    }

}