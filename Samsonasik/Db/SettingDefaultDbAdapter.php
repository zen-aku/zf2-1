<?php
/*
* The price of flexibility in Zend Framework 2 often push us to setting all ‘manually’. 
* We have to be a creative person, right ? In this post, i want to present you my greatest post ! 
* Simple, but very important because it can reduce your code redundancy in setting up DbAdapter to Your Table Class.
*/
//Without this trick, you have to do the following sooooooo extra and redundant code:
'factories'=> array(
    'ModuleName\Model\TableA' =>  function($sm) {
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        $table     = new \ModuleName\Model\TableA($dbAdapter);
        return $table;
    },
    'ModuleName\Model\TableB' =>  function($sm) {
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        $table     = new \ModuleName\Model\TableB($dbAdapter);
        return $table;
    },
    'ModuleName\Model\TableC' =>  function($sm) {
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        $table     = new \ModuleName\Model\TableC($dbAdapter);
        return $table;
    },
),
			
// Do you want to know how to change that to ‘only’ :
'invokables'=>array(
    'ModuleName\Model\TableA' => 'ModuleName\Model\TableA',
    'ModuleName\Model\TableB' => 'ModuleName\Model\TableB',
    'ModuleName\Model\TableC' => 'ModuleName\Model\TableC',
),

/*			
 * I’m sure, very very very sure !!!, you want to know the trick !.
 * Firstly, you need to create your common service Factory to your need in your application.
 * This Service Factory will set your controller and inject a property named $dbAdapter for your controller that will be instantiated.
 */
namespace ZfCommons\Service;
 
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
 
class CommonServiceFactory implements FactoryInterface {
    protected $controller;
     
    public function createService(ServiceLocatorInterface $services) {
        $serviceLocator = $services->getServiceLocator();
        $dbAdapter = $serviceLocator->get('Zend\Db\Adapter\Adapter');      
        $controller = new $this->controller;
        $controller->setDbAdapter($dbAdapter);    
        return $controller;
    }
     
    //setter controller 
    public function setController($controller) {
        $this->controller = $controller;
    }
}
 
/**
 * For reduce re-code your function to setDbAdapter(), just create your master controller.
 * which all controller that you created can extends.
 */
namespace ZfCommons\Controller; 
 
use Zend\Mvc\Controller\AbstractActionController;
 
class MasterController extends AbstractActionController{
    protected $dbAdapter  ;
     
    public function setDbAdapter($db) {
        $this->dbAdapter = $db;
    }
}

// Next, create your Table Model class :
namespace Test\Model;
 
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
 
class TrackTable extends AbstractTableGateway {
    protected $table = 'tracks';
    
    public function __invoke(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->initialize();
    }
     
    public function fetchAll() {
        $resultSet = $this->select();
        return $resultSet->toArray();
    }
}

//Last step, register that in module config :
return array(
/* router */
/* view_manager */
/* di */
'controllers' => array(
    'factories' => array(
         'Test\Controller\Album' => function($sm){
              $commservice = new \ZfCommons\Service\CommonServiceFactory();
              $commservice->setController('\Test\Controller\AlbumController');
               
              return $commservice->createService($sm);  
           },
      ),
),
'service_factory' => array(
    'invokables' => array(
       //register model table classes here....
       'Test\Model\TrackTable' => 'Test\Model\TrackTable'
     ),
),
				   
// Finally, test with create your controller which extends your MasterController :
namespace Test\Controller;
 
use ZfCommons\Controller\MasterController;
 
class AlbumController extends MasterController {
	
    public function indexAction() {
        $tracktable =  $this->getServiceLocator()->get('Test\Model\TrackTable');
        //dbAdapter will automatically getted !
        $tracktable($this->dbAdapter);      
        $result = $tractable->fetchAll();
        //just test <span class="wp-smiley wp-emoji wp-emoji-wink" title=";)">;)</span>
        foreach($result as $key=>$row){
            echo $row['song_title'];
        }
    }
}