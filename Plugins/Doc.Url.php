<?php

class IndexController extends AbstractActionController {
    function indexAction() {
		
		/*
		 * Возвращает объект класса-плагина Zend\Mvc\Controller\Plugin\Url
		 */
		$this->url();
		
		/*
		 * Url::fromRoute($route = null, $params = array(), $options = array(), $reuseMatchedParams = false)
		 * Генерирует Url из $route, $params и $options
		 * $route - имя ключа массива роута в конфиге
		 */
		$url = $this->url()->fromRoute('event-show-index');
		
	}
}

/*
 * Плагин url()->fromRoute() ведёт себя аналогично хелперу url()
 * Плагин url()->fromRoute() используется для создания строкового представления маршрутов, которые вы определяете в вашем приложении.
 * fromRoute()($name = null, $params = array(), $options = array(), $reuseMatchedParams = false)
 *		$name - название маршрута (имя массива роута в конфиг-роутере): 'helpers-index'
 *		$params - массив параметров роутерa: ['action' => 'url', 'id' => 2] - /index/url/2
 *		$options - массив опций роутера: ['fragment' => 'comments'] - /index/url/2#comments
 *		$reuseMatchedParams - флаг, показывающий использовать параметры текущего урла при создании строки урла   
 */
 /*
  *  У нас есть такой роутер:
  * 'routes' => array(
		// Segment route '<module name>-<controller name>'
		'helpers-index' => array(
			'type'    => 'Segment',
			'options' => array(
				// route' => '/<modulename>/<controller name>[/:action[/:id]]
				'route'    => '/helpers/index[/:action[/:id]]',
				'defaults' => array(
					'__NAMESPACE__'	=> 'Helpers\Controller',
					'controller' => 'Index',
					'action' => 'index',
				),
				// optional constraints
				'constraints' => array(
					// 'action' => '(<action name1>|<action name2>|...)',
					'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
					//'action' => '(index)',
					// 'id' => '<regexp param>'
					'id' => '[0-9]+',
				),
			),
		),
 */

// '/helpers/index'
$this->url()->fromRoute('helpers-index');
	
// '/helpers/index/url/2'
$this->url()->fromRoute('helpers-index', ['action' => 'url', 'id' => 2]);
	
// '/helpers/index/url?page=13'
$this->url()->fromRoute( 'helpers-index', ['action' => 'url'], ['query' => ['page' => 13]] );
		
// '/helpers/index/url#comments'
$this->url()->fromRoute('helpers-index', ['action' => 'url'], ['fragment' => 'comments'] );
		

/*
Если текущий урл с какими-то параметрами: /helpers/index/url, то чтобы его сохранить
в урле надо в параметр $reuseMatchedParams передать флаг true и ['action' => 'url'] прикрепится к урлу
"/helpers/index/url"
*/
$this->url()->fromRoute('helpers-index', [], null, true);

//Третий параметр можно вообще опустить перед флагом 
$this->url()->fromRoute('helpers-index', [], true);