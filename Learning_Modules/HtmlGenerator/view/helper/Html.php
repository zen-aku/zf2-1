<?php

namespace HtmlGenerator\View\Helper;

/**
 * Фабрика прототипов - html-элементов.
 * Перехватчик статических методов - вызовов объектов html-элементов из хранилища прототипов
 */
class Html {

	/**
     * @const int Число пробелов в табуляции для форматирования сгенерированного html-кода
     */
    const NBSP_IN_TAB = 4;
	
    /**
     * Массив имён html-элементов и путей их классов ['html-элемент' => 'путь к классу']
     * @var array
     */   
    private static $htmlElementClasses = array (
        'a'     => 'HtmlGenerator\View\Helper\A',
        'br'    => 'HtmlGenerator\View\Helper\Br',
        'span'  => 'HtmlGenerator\View\Helper\Span',  
    ); 
    
    /**
	 * Массив прототипов html-элементов 
	 * @var array Zend\View\Helper\AbstrHtmlElement arrayCloneTags['div'=> Div, 'a' => A, ]
	 */
	private static $arrayCloneTags = array();
    
	/**
	 * @var bollean В данном теге присутствует контент
	 */
	private static $isContent = false;
	
	/**
	 * @var int Число незакрытых тэгов
	 */
	private static $count = 0;

	/**
	 * @var array Массив имён незакрытых html-элементов 
	 */
	private static $arrayTags;
      
	/**
     * Сгенерировать html-элемент AbstrHtmlElement (клонировать из хранилища и сгенерировать открывающий тэг)
     * @param type $htmlElement - имя перехваченного метода, одноимённого с html-элементом
     * @param type $arg $arg[0] - массив аттрибутов html-элемента
     * @throws \Exception
     * @return Zend\View\Helper\AbstrHtmlElement
     */   
	static function __callStatic(  $htmlElement, $arg = array() ) {
		
        // Проверка существования элемента $htmlElement в массиве классов html-элементов self::$htmlElementsClasses
        if ( !key_exists($htmlElement, self::$htmlElementClasses) ) {
             throw new \Exception ('Для Html-элемента '.$htmlElement.' не существует прототипа в хранилище');
        }
        // Создать объект класса self::$htmlElementsClasses[$htmlElement] и поместить его в хранилище прототипов self::$arrayCloneTags
        if ( !isset( self::$arrayCloneTags[$htmlElement] ) ) {           
            self::$arrayCloneTags[$htmlElement] = new self::$htmlElementClasses[$htmlElement]();
        }
        // Клонировать html-элемент
        $tag = clone self::$arrayCloneTags[$htmlElement];
        
        // Передать html-элементу $tag массив аттрибутов $arg[0]
        if ( isset($arg[0]) && is_array($arg[0]) ) {
            $tag->attrs($arg[0]);
        }     
       
		// Сгенерировать открывающий тэг
		echo PHP_EOL . self::getTab() . $tag->getOpenTag();
		
		// Cохранить в массиве имя закрывающего тэга, если у элемента есть закрывающий тэг
        if ( $tag->isCloseTag() ) {
			self::$arrayTags[self::$count] = $tag->getName();
			self::$count++;
        }
		unset($tag);		
	}
  
    /**
	 * Извлечь последний открытый html-элемент и сгенерировать его отформатированный закрывающий тег
	 * @return boolean - есть незакрытые теги или нет
	 */
	static function end( $tag = null ) { 
		
		$count = self::$count-1;
		
		if ( isset(self::$arrayTags[$count]) ) {		
			$nameTag = self::$arrayTags[$count];			
			// Проверка совпадения заданного закрывающего тэга с тем, что стоит в очереди на закрытие
			if ( $tag != null and $nameTag != $tag ) {
				throw new \Exception('Неправильно задан закрывающий тэг </'.$tag.'>. Должен быть </'.$nameTag.'>');
			}
			self::$count = $count;

			if (self::$isContent) {
				$format = '';
				self::$isContent = false;
			} else {	
				$format = PHP_EOL . self::getTab();			
			}
			
			echo $format.'</'.$nameTag.'>';
			unset(self::$arrayTags[$count]);
		}
	}
    
    /**
     * Сгенерировать контент $content
     * @param mixed $content
     */
    static function content($content) {
		self::$isContent = true;
		echo $content;
    }
 	
	/**
     * Вернуть количество табуляций равное числу незакрытых тегов - для форматирования тегов
     * @return string
     */
    private static function getTab() {
		$count = self::NBSP_IN_TAB * self::$count;
		$tab = '';
        for ( $i = 0; $i < $count; $i++ ) {
            $tab .= ' '; 
        } 
        return $tab;
    }
	
}