<?php
/*
 * Хелпер headLink() используется для создания связей с различными ресурсами вашего сайта, 
 * таких как: stylesheets, feeds, favicons, trackbacks и многие другие. 
 * Помощник HeadLink предоставляет простой интерфейс для создания и агрегирования этих элементов для последующего извлечения и вывода в макете(layout).
 * Хелпер для хранения и формирования отформатированного вывода (в соответствии с прописанными отступом, префиксом, разделителем и отступом) тегов <link>
 * использует контейнер Zend\View\Helper\Placeholder\Container\AbstractContainer и представляет соьой частный случай хелпера placeholder() (см. Doc.Placeholder.php)
 */

/*
 * Первый способ добавления линка в хранилище - через __invoke() - объект линка ((object)$attributes) 
 * добавляются в контейнер AbstractContainer (ArrayObject) как элементы массива с числовым индексом
 * __invoke(array $attributes = null, $placement = Placeholder\Container\AbstractContainer::APPEND)
 *		$attributes	- массив аттрибутов линка со значениями, преобразуется в std-объект со свойствами-аттрибутами
 *			и заносится в контейнер с помошью AbstractContainer::append($object). 
 *			Поэтому нельзя напрямую заносить массив аттрибутов через $this->headlink()->append($attributes),
 *			а только через appendStylesheet() или напрямую в headLink()
 *		$placement - APPEND (по умолчанию), SET или PREPEND, которые определяют соответствующую функцию AbstractContainer	
 */

$this->headLink(array('rel' => 'icon', 'href' => '/img/favicon_set.ico'), 'SET'); 
$this->headLink(array('rel' => 'icon', 'href' => '/img/favicon_prepend.ico'), 'PREPEND'); 
$this->headLink(array('rel' => 'icon', 'href' => '/img/favicon_append.ico'));
//echo $this->headLink();

/*
 * Второй способ добавления линка относится только к стилям:
 * appendStylesheet($href, $media, $conditionalStylesheet, $extras)
 *  $href - ссылка
 *  $media - по умолчанию screen (может быть напр. print)
 *  $conditionalStylesheet - true или IE-платформа (прописывется в выводе как комментарий) к которой относится данный стиль
 *  $extras - массив с любыми дополнительными значениями, которые необходимо добавить в тег.
 *  Альтернативные линки: методы-аналоги стилевых appendAlternate, offsetSetAlternate, prependAlternate и setAlternate
 */
$this->headLink()
	//->setStylesheet('/styles/set.css')		// замещает весь массив контейнера на новое значение
    ->appendStylesheet('/styles/append.css')	// добавляет в конец массива-контейнера
    ->prependStylesheet(						// добавляет в начало массива-контейнера
        '/styles/moz.css',
        'screen',
        true,
        array('id' => 'my_stylesheet')
    );

/*
 * offsetSetStylesheet($index, $href, $media, $conditionalStylesheet, $extras)
 * $index - числовой индекс в контейнере куда будет добавлен стиль
 */
$this->headLink()->offsetSetStylesheet(
    1,
    '/styles/offset.css',
    'screen',
    true,
    array('id' => 'my_offset')
);
// rendering the links from the layout:
//echo $this->headLink();
