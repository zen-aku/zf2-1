<?php
/*
 * I like is about service initializer. It can inject to all instance of something, for example, if Model is extends AbstractTableGateway.
 * For example, you have SampleTable class like the following :
 */

class SampleTable extends AbstractTableGateway implements \Zend\Db\Adapter\AdapterAwareInterface {
    
	protected $table = 'sampletable';
  
    public function setDbAdapter(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new HydratingResultSet();        
        $this->resultSetPrototype->setObjectPrototype(new Sample());
        $this->initialize();
    }
	
}

/*
 * So, you can initialize of instantiation of all class that implement AdapterAwareInterface like SampleTable.
 */

class Module  {
	
    public function getServiceConfig() {
		return array(
			'initializers' => array(
				function ($instance, $sm) {
					if ($instance instanceof \Zend\Db\Adapter\AdapterAwareInterface) {
						$instance->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
					}
				}
			),

			'factories' => array(
				'SampleModule\Model\SampleTable' =>  function($sm) {
					$table     = new Model\SampleTable();
					return $table;
				},
			),

		);
    }  
	
}