<?php

namespace HtmlGenerator\View\Helper;

//use Zend\View\Helper\AbstrHtmlElement;

/**
 * Html-элемент <a></a>
 */
class A extends AbstrHtmlElement {
	
    /**
     * Массив разрешённых локальных аттрибутов элемента <a>
     * @var array
     */
    protected $accessLocalAttributes = array(
        'href',
    );
    	
    /**
     * Имя текущего html-элемента
     * @var string 
     */
    protected $name = 'a';
    
}
