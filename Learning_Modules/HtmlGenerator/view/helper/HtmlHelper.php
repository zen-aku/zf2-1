<?php

namespace HtmlGenerator\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Хелпер-Фабрика прототипов - html-элементов.
 * Перехватчик статических методов - вызовов объектов html-элементов из хранилища прототипов
 */
class HtmlHelper extends AbstractHelper {

	/**
     * @const int Число пробелов в табуляции для форматирования сгенерированного html-кода
     */
    const NBSP_IN_TAB = 4;
	
    /**
     * Массив имён html-элементов и путей их классов ['html-элемент' => 'путь к классу']
     * @var array
     */   
    private $htmlElementClasses = array (
        'a'     => 'HtmlGenerator\View\Helper\A',
        'br'    => 'HtmlGenerator\View\Helper\Br',
        'span'  => 'HtmlGenerator\View\Helper\Span',  
    ); 
    
    /**
	 * Массив прототипов html-элементов 
	 * @var array Zend\View\Helper\AbstrHtmlElement arrayCloneTags['div'=> Div, 'a' => A, ]
	 */
	private $arrayCloneTags = array();
    
	/**
	 * @var bollean В данном теге присутствует контент
	 */
	private $isContent = false;
	
	/**
	 * @var int Число незакрытых тэгов
	 */
	private $count = 0;

	/**
	 * @var array Массив имён незакрытых html-элементов 
	 */
	private $arrayTags;
      
	/**
     * Сгенерировать html-элемент AbstrHtmlElement (клонировать из хранилища и сгенерировать открывающий тэг)
     * @param type $htmlElement - имя перехваченного метода, одноимённого с html-элементом
     * @param type $arg $arg[0] - массив аттрибутов html-элемента
     * @throws \Exception
     * @return Zend\View\Helper\AbstrHtmlElement
     */   
	function __call(  $htmlElement, $arg = array() ) {
		
        // Проверка существования элемента $htmlElement в массиве классов html-элементов $this->htmlElementsClasses
        if ( !key_exists($htmlElement, $this->htmlElementClasses) ) {
             throw new \Exception ('Для Html-элемента '.$htmlElement.' не существует прототипа в хранилище');
        }
		
        // Создать объект класса $this->htmlElementsClasses[$htmlElement] и поместить его в хранилище прототипов $this->arrayCloneTags
        if ( !isset( $this->arrayCloneTags[$htmlElement] ) ) {           
            $this->arrayCloneTags[$htmlElement] = new $this->htmlElementClasses[$htmlElement]();
        }
        // Клонировать html-элемент
        $tag = clone $this->arrayCloneTags[$htmlElement];       
		
        // Передать html-элементу $tag массив аттрибутов $arg[0]
        if ( isset($arg[0]) && is_array($arg[0]) ) {
            $tag->attrs($arg[0]);
        }     
       
		// Сгенерировать открывающий тэг
		echo PHP_EOL . $this->getTab() . $tag->getOpenTag();
		
		// Cохранить в массиве имя закрывающего тэга, если у элемента есть закрывающий тэг
        if ( $tag->isCloseTag() ) {
			$this->arrayTags[$this->count] = $tag->getName();
			$this->count++;
        }
		unset($tag);		
	}
  
    /**
	 * Извлечь последний открытый html-элемент и сгенерировать его отформатированный закрывающий тег
	 * @return boolean - есть незакрытые теги или нет
	 */
	function end( $tag = null ) { 
		
		$count = $this->count-1;
		
		if ( isset($this->arrayTags[$count]) ) {		
			$nameTag = $this->arrayTags[$count];			
			// Проверка совпадения заданного закрывающего тэга с тем, что стоит в очереди на закрытие
			if ( $tag != null and $nameTag != $tag ) {
				throw new \Exception('Неправильно задан закрывающий тэг </'.$tag.'>. Должен быть </'.$nameTag.'>');
			}
			$this->count = $count;

			if ($this->isContent) {
				$format = '';
				$this->isContent = false;
			} else {	
				$format = PHP_EOL . $this->getTab();			
			}
			
			echo $format.'</'.$nameTag.'>';
			unset($this->arrayTags[$count]);
		}
	}
    
    /**
     * Сгенерировать контент $content
     * @param mixed $content
     */
    function content($content) {
		$this->isContent = true;
		echo $content;
    }
 	
	/**
     * Вернуть количество табуляций равное числу незакрытых тегов - для форматирования тегов
     * @return string
     */
    private function getTab() {
		$count = self::NBSP_IN_TAB * $this->count;
		$tab = '';
        for ( $i = 0; $i < $count; $i++ ) {
            $tab .= ' '; 
        } 
        return $tab;
    }
	
}