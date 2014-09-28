<?php

namespace HtmlGenerator\View\Helper;

//use Zend\View\Helper\AbstrHtmlElement;

/**
 * Html-элемент <a></a>
 */
class Br extends AbstrHtmlElement {
	 	
    /**
     * Имя текущего html-элемента
     * @var string 
     */
    protected $name = 'br';
    
    /**
     * Флаг - есть закрывающий тег
     * @var boolean 
     */
    protected $isCloseTag = false;
    
}
