<?php
/*
 * Хелпер layout() - это полный аналог плагина контроллера Zend\Mvc\Controller\Plugin\Layout::__invoke($template = null)
 * Позволяет изменить шаблон вида для данного экшена из вида.
 * Новый шаблон должен быть прописан в конфиге в 'template_map'
 * Если новый шаблон не задан, то вызывается шаблон по стандартному пути для данного экшена
 */
$this->layout('layout/EventsLayout');
//$this->layout();

/*
* Аналог предыдущего задания шаблона, но с обязательным аргументом $template
*/
//$this->layout()->setTemplate('layout/EventsLayout');
