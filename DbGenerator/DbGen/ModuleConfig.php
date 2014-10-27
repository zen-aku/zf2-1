<?php
namespace DbGen;

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
		$arrayRouter = include __DIR__ . '/config/module.getConfig.router.php';
		$arrayConfig = include __DIR__ . '/config/module.getConfig.php';
		return array_merge($arrayConfig, $arrayRouter);
		
		// Конфидициальные настройки модуля, которые не предназначеня для хранения в репозитарии.
		//$arrayLocal = include __DIR__ . '/config/module.getConfig.local.php';
		//return array_merge($arrayConfig, $arrayRouter, $arrayLocal);
	}

	//ControllerProviderInterface
	function getControllerConfig() {
		return include __DIR__ . '/config/module.getControllerConfig.php';
	}

	// ServiceProviderInterface
	function getServiceConfig() {
		return include __DIR__ . '/config/module.getServiceConfig.php';
	}

	// ViewHelperProviderInterface
	function getViewHelperConfig() {
		return include __DIR__ . '/config/module.getViewHelperConfig.php';
	}
    
    // ControllerPluginProviderInterface
    function getControllerPluginConfig() {
        return include __DIR__ . '/config/module.getControllerPluginConfig.php';
    }

}