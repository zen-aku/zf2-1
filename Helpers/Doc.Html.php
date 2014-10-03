<?php
/*
 * Html-хелперы построены на основе Zend\View\Helper\AbstractHtmlElement, расширяя который можно делать свои html-хелперы
 */
 
/* 
 * 1. Хелпер htmlList() - генерирует список ol/ul
 * __invoke(array $items, $ordered = false, $attribs = false, $escape = true)
 *      $items - массив элементов списка 
 *      $ordered - тип тега (ol - true, ul - false)
 *      $attribs - массив аттрибутов тега ol/ul (напр. ['type' => 'a', 'reversed'=>'reversed'])
 *      $escape - экранирует (преобразует вредные символы в кодировки) контент и аттрибуты используя хелперы escapeHtml() и escapeHtmlAttr() 
 */
$list = ['список1<инъекция>','список2','список3','список4','список5',];

echo $this->htmlList( $list, true, ['type' => 'a', 'reversed'=>'reversed'] ); // вместо <> используются кодировки &lt;&gt;
echo $this->htmlList( $list, false, ['type' => 'square'], false );

/*
 * 2. Хелпер htmlObject() - генерирует тег <object> для любых типов объектов
 * __invoke($data = null, $type = null, array $attribs = array(), array $params = array(), $content = null)
 *      $data - url объекта(файла) для аттрибута data : "helloworld.swf"
 *      $type - значение аттрибута type : "media_type"
 *      $attribs - массив аттрибутов: ['width'=>"400", 'height'=>"400"]
 *      $params - массив дополнительных параметров тега 
 *      $content - контент внутри тегов <object>контент<object>
 */
echo $this->htmlObject(
    'helloworld.swf',
    "media_type",
    ['width'=>"400", 'height'=>"400"],
    [],
    'Какой-то контент'
);

/*
 * 2.1.   Хелпер htmlFlash() - генерирует тег <object> для встраивания флэш-файлов. Он расширяет хелпер htmlObject()
 * __invoke($data, array $attribs = array(), array $params = array(), $content = null)
 *      $data - url флэш-файла
 *   Аттрибут type автоматически устанавливается в значение type="application/x-shockwave-flash"   
 */
echo $this->htmlFlash('/path/to/flash.swf', ['width'=>"100", 'height'=>"100"]);

/*
 * 2.2.   Хелпер htmlPage() - генерирует тег <object> для встраивания других html страниц. Он расширяет хелпер htmlObject()
 * __invoke($data, array $attribs = array(), array $params = array(), $content = null)
 *      $data - url html
 *   Аттрибут type автоматически устанавливается в значение type="text/html"   
 */
echo $this->htmlPage('/path/to/page.html', ['width'=>"100", 'height'=>"100"]);

/*
 * 2.3.   Хелпер htmlQuicktime() - генерирует тег <object> для встраивания Quicktime файлов. Он расширяет хелпер htmlObject()
 * __invoke($data, array $attribs = array(), array $params = array(), $content = null)
 *      $data - url html
 *   Аттрибут type автоматически устанавливается в значение type="video/quicktime"   
 */
echo $this->htmlQuicktime('/path/to/quicktime.q');
