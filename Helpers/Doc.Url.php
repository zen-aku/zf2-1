<?php
/*
 * Хелпер url() используется для создания строкового представления маршрутов, которые вы определяете в вашем приложении.
 * __invoke($name = null, $params = array(), $options = array(), $reuseMatchedParams = false)
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
?>
<style>
	a{display: block;}
</style>

<!-- 
<a href="/helpers/index"></a> 
-->
<a href="<?php echo $this->url('helpers-index'); ?>">
	'/helpers/index'
</a>

<!-- 
<a href="/helpers/index/url/2"></a> 
-->
<a href="<?php echo $this->url('helpers-index', ['action' => 'url', 'id' => 2]); ?>">
	'/helpers/index/url/2'
</a>

<!-- 
<a href="/helpers/index/url?page=13"></a> 
-->
<a href="<?php echo $this->url('helpers-index', ['action' => 'url'], ['query' => ['page' => 13]] ); ?>">
	'/helpers/index/url?page=13'	
</a>

<!-- 
<a href="/helpers/index/url#comments"></a> 
-->
<a href="<?php echo $this->url('helpers-index', ['action' => 'url'], ['fragment' => 'comments'] ); ?>">
	'/helpers/index/url#comments'	
</a>

<!--
Если текущий урл с какими-то параметрами: /helpers/index/url, то чтобы его сохранить
в урле ссылки надо в параметр $reuseMatchedParams передать флаг true и ['action' => 'url'] прикрепится к ссылке
<a href="/helpers/index/url"></a>
-->
<a href="<?php echo $this->url('helpers-index', [], null, true); ?>">
	'/helpers/index/url'
</a>

<!-- Третий параметр можно вообще опустить перед флагом -->
<a href="<?php echo $this->url('helpers-index', [], true); ?>">
	'/helpers/index/url'
</a>