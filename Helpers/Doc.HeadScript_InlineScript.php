<?php
/*
 * Хелпер headScript() генерирует тег <script> в <head>.
 * Его клоном является хелпер inlineScript(), обладающий тем жефункционалом что и headScript(), 
 * но его рекомендуется использовать в теле <body></body> вместо headScript(), который применяют в теле <head>
 * Для хранения он использует контейнер Zend\View\Helper\Placeholder\Container\AbstractContainer
 * Хелпер headscript() представляет собой частный случай более универсального хелпера placeholder()
 */
/*
 * Первый способ добавления тега <script> в хранилище - через __invoke() - meta добавляются в контейнер как числовые элементы массива
 * __invoke($mode = self::FILE, $spec = null, $placement = 'APPEND', array $attrs = array(), $type = 'text/javascript')
 *      $mode - 'FILE' - тег со ссылкой на ресурс скрипта (src = $spec) или 'SCRIPT' - код $spec скрипта внутри тегов
 *      $spec - код скрипта внутри тегов <script> если $mode = 'SCRIPT' или ссылка на ресурс скрипта если $mode = 'FILE'
 *      $placement - способ добавления тега в контейнер-хранилище: 'APPEND', 'SET', 'PREPEND'
 *      $attrs - массив аттрибутов тега <script> ('charset', 'defer', 'language'): ['charset'=>'utf-8']
 *      $type - значение аттрибута 'type' (text/javascript (default), text/ecmascript, application/ecmascript, application/javascript)
 */
// подгрузка скрипта с ресурса
$this->headScript(
    'FILE',
    '/js/my.js',
    'PREPEND',
    ['charset'=>'utf-8'],
    'application/javascript'  
);
// код скрипта inline (в тегах)
$script = 'var scale_field = 5.8; //какой-то код скрипта';
$this->headScript(
    'SCRIPT',
    $script  
);
//echo $this->headScript();

$this->inlineScript(
    'SCRIPT',
    $script  
);
//echo $this->inlineScript();

/*
 * Второй способ добавления тега скрипт в хранилище - через индивидуальные методы хелпера, 
 * соответствующие способу подключения кода скрипта в тег: 
 *   FILE - из внешних ресурсов (embedded)
 *      appendFile($src, $type = 'text/javascript', $attrs = array()) - $src - url ресурса, $attrs - массив доп. аттрибутов('charset', 'defer', 'language')
 *      offsetSetFile($index, $src, $type = 'text/javascript', $attrs = array()) - вставить в контейнер в позицию $index
 *      prependFile($src, $type = 'text/javascript', $attrs = array())
 *      setFile($src, $type = 'text/javascript', $attrs = array()) - заменить весь массив контейнера тегов-скриптов на новое значение
 *   SCRIPT - внутрь тега (inline)
 *      appendScript($script, $type = 'text/javascript', $attrs = array()) - $script - код скрипта (можно инклудить или считывать с какого-нибудь файла js)
 *      offsetSetScript($index, $script, $type = 'text/javascript', $attrs = array())
 *      prependScript($script, $type = 'text/javascript', $attrs = array())
 *      setScript($script, $type = 'text/javascript', $attrs = array())
 *  Все эти методы вызывают соответствующие методы AbstractContainer (напр. AbstractContainer::append())
 */
$this->headScript()->appendFile('/js/prototype.js');  // тег с подгрузкой скрипта script type="text/javascript" src="/js/prototype.js"></script>
$this->headScript()->offsetSetScript(3, $script, 'text/javascript', ['charset'=>'utf-8']);  // скрипт инлайн


/*
 * Буфферизация (захват) скриптов для более позднего их вывода.
 * captureStart($captureType = Placeholder\Container\AbstractContainer::APPEND, $type = 'text/javascript', $attrs = array())
 */
// начать буфферизацию 
$this->headScript()->captureStart('PREPEND');
// буфферизуемый крипт:
?>
var action = '<?php echo $this->baseUrl ?>';
$('foo_form').action = action;
<?php 
// остановить буфферизацию и добавить скрипт в хранилище методом, указанным в captureStart() (по умолчанию APPEND)
$this->headScript()->captureEnd('PREPEND');
echo $this->headScript();

/*
 * Передаваемый контент в хелпер автоматически экранируется. Чтобы отключить автоматическое экранирование
 * надо использовать метод: setAutoEscape(false)
 */
$this->headscript()->setAutoEscape(false);