<?php
namespace TestMysqlGenerator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use MysqlGenerator\Sql\Predicate;

/**
 *
 */
class SqlController extends AbstractActionController {

	/**
	 * router "/testmysqlgenerator/sql/index"
	 */
    function indexAction() {
        
        $adapter = $this->getServiceLocator()->get('MysqlGenerator\Adapter\Adapter');
        		
		$sql = new \MysqlGenerator\Sql\Sql($adapter);
        //$sql = $this->getServiceLocator()->get('Zend\Db\Sql\Sql');
		 
        $select = $sql->select();
        $select->from('users');
        $select->where( (new Predicate\Predicate())
		 		->in('id', [1, 2, 3])
				->between('id', 2, 5)
				->like('login', 'Петр%')
				->orPredicate(new Predicate\Like('login', 'Иван%'))
		);
        
        // Сделать запрос, используя подготовленное выражение
        $statement = $sql->prepareStatementForSqlObject($select)->execute();
        $result = $statement->getResource()->fetchAll(\PDO::FETCH_ASSOC);
		
		
    	return new ViewModel(
			array(
				'result' => $result,
			)
    	);   
    }

}