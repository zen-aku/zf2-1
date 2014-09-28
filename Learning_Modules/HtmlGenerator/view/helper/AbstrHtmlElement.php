<?php

namespace HtmlGenerator\View\Helper;

/**
 * Абстракция html-элемента
 */
abstract class AbstrHtmlElement {
	     
    /**
     * Флаг - у html-элемента должен быть закрывающий тег
     * @var boolean 
     */
    protected $isCloseTag = true;
    
    /**
     * Закрывающая скобка открывающего тега html-элемента,
     * Если html-элемент должен иметь закрывающий тег - '>' , иначе - ' />' (для совместимости с xhtml)
     * @var string
     */
    protected $closingBracket = '>';
    
    /**
     * Массив разрешённых локальных аттрибутов элемента конкретного html-элемента
     * @var array
     */
    protected $accessLocalAttributes = array();
      
    /**
     * Массив разрешённых глобальных аттрибутов для всех html-элементов
     * @var array
     */
    protected $accessAttributes = array(
        'id',
        'class',
    );
    
    /**
     * Массив  аттрибутов ['id'=> [value] , 'href'=> [value], ... ]
     * @var array
     */
    protected $attributes = array();
    
    /**
     * Прикрепить к глобальному массиву разрешённых аттрибутов массив разрешённых локальных аттрибутов данного класса html-элемента
     * Задать закрывающую скобку элементу в зависимости от наличия закрывающего тега
     * @param \Zend\View\Helper\HtmlPrototypes $htmlPrototypes
     */
    function __construct() {
        $this->accessAttributes = array_merge($this->accessAttributes, $this->accessLocalAttributes);        		
        if ( !$this->isCloseTag ) {
            $this->closingBracket = ' />';
        } 
    }
  
	/**
	 * Вернуть имя html-элемента
	 * @return string Имя html-элемента
	 */
	function getName() {
		return $this->name;
	}
	
    /**
     * Добавить аттрибут в массив аттрибутов $this->attributes
     * @param array $attributes
     */
    function attrs( $attributes = array() ) {
        foreach ( $attributes as $attribute => $value ) {
            if ( !in_array( $attribute, $this->accessAttributes ) ) { 
				throw new \Exception ('Для класса html-элемента '.__CLASS__.' недопустима задание аттрибута '. $attribute);
			}    
			$this->attributes[$attribute] = $value;
        }
    }
    
    /**
	 * Собрать и вернуть открывающий тег html-элемента 
	 * @return string
	 */
    function getOpenTag() {      
        $attributes = '';
        foreach ($this->attributes as $attribute => $value) {
            $attributes .= ' '.$attribute.'="'.$value.'"';
        }       
        return '<' . $this->name . $attributes . $this->closingBracket;
	}
 
    /**
     * У элемента должен быть закрывающий тэг?
     * @return boolean
     */
    function isCloseTag() {
        return $this->isCloseTag;
    }
    
}