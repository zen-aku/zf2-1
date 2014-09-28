<?php
namespace Helpers;

use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\EventManager\EventInterface;

require 'ModuleConfig.php';

class Module
	extends ModuleConfig
	implements InitProviderInterface,  BootstrapListenerInterface
{

	/**
	 *
	 */
	function init( ModuleManagerInterface $moduleManager ) {

	}

	/**
	 *
	 */
	function onBootstrap( EventInterface $e ) {

	}
}