<?php

namespace HtmlGenerator\View\Helper;

//use Zend\View\Helper\AbstrHtmlElement;

/**
 * Html-элемент <span></span>
 */
class Span extends AbstrHtmlElement {
	
    /**
     * Массив разрешённых локальных аттрибутов элемента <span>
     * @var array
     */
    protected $accessLocalAttributes = array(
       
    );
    
    /**
     * Имя текущего html-элемента
     * @var string 
     */
    protected $name = 'span';
    
}


