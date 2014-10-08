<?php
namespace Helpers\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 */
class ViewsController extends AbstractActionController {

	/**
     * route: helpers/views
     * Пример добавления в объект вида ViewModel шаблонов(детей) - объектов вида ViewModel с помощью метода 
     * ViewModel::addChild(ModelInterface $child, $captureTo = null, $append = null)
     *  $child - объект класса ViewModel, в котором находится шаблон
     *  $captureTo - имя, под которым сохраняется дочерний шаблон в родителе - объекте вида ViewModel
     *  $append - Set flag indicating whether or not append to child  with the same capture
	 * 
     * В виде в нужном месте кода можно будет вызвать шаблон по имени $captureTo
     * с помощью хелпера renderChildModel($captureTo) (см. index.phtml)
	 */
    function indexAction() {
            
        $view = new ViewModel( 
            array(
                'data' => 'какие-то данные',
            ) 
        );
		
		//Создаём шаблон вызовом другого экшена(см. плагин forward() (Doc.Forward.php))
        $gravatar = $this->forward()->dispatch('Helpers\Controller\Index', ['action' => 'gravatar']);
     
        // создаём объект шаблона ViewModel через переменную $article и передаём ему шаблон
        $article = new ViewModel();      
        $article->setTemplate('layout/ArticleTemplate'); 
       
        $view->addChild( $article, 'article' )
             // создаём объект ViewModel на лету и передаём ему шаблон
             ->addChild( (new ViewModel())->setTemplate('layout/SidebarTemplate'), 'sidebar')
			 ->addChild( $gravatar, 'gravatar' );	
		
    	return $view;
    }
    
}