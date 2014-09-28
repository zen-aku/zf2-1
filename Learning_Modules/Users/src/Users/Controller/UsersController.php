<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class UsersController extends AbstractActionController {
	
    public function indexAction() {
        return array();
    }

    public function fooAction() {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /module-specific-root/Users/foo
        return array();
    }
}
