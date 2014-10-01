<?php

/*
 * Хелпер-контейнер placeholder() спользуется для сохранения содержимого между скриптом вида и отображением. 
 * placeholder()->_invoke($key) возвращает контейнер класса Zend\View\Helper\Placeholder\Container\AbstractContainer extends ArrayObject 
 * из своего свойства массива с ключом $key: $this->items['foo'] = <AbstractContainer(value)> 
 * Так же имеются несколько полезных функций:
 *      - объединение содержимого, 
 *      - сохранение содержимого скрипта вида для последующего использования, 
 *      - добавления пред- и пост- текста, 
 *      - добавление разделителей
 */

/* 
 * создать контейнер для ключа 'foo' контейнер и добавить в него значение "Some text for later"
 * $this->items['foo'] = new AbstractContainer() и AbstractContainer::set("Some text for later")
 * AbstractContainer::set($value) { $this->exchangeArray(array($value)); } - Заменяет текущий массив (array) на другой массив (array) или объект (object)
 */ 
 // сохранить одно сообщение как единственный нулевой элемент ArrayObject[0]
 $this->placeholder('foo')->set("Some text for later");
 // сохранить массив сообщений
 $this->placeholder('foo')->exchangeArray(array(1,2,3,4,5));
 // вызываем содержимое всего массива
 echo $this->placeholder('foo');
 
 // можно сохранять в отдельную ячейку(свойство) ArrayObject
 $this->placeholder('foo')->bar = 'Some text for later';
 $this->placeholder('foo')['bar'] = 'Some text for later';
 $this->placeholder('foo')->offsetSet('bar', 'Some text for later');
 // вызываем из отдельного свойства(ячейки) ArrayObject
 echo $this->placeholder('foo')->bar;
 echo $this->placeholder('foo')['bar'];     
 echo $this->placeholder('foo')->offsetGet('bar');
 

/*
 * Зададим отступ(indent), префикс(prefix), постфикс(postfix) и разделитель(separator):
 */
$this->placeholder('foo')->setPrefix("<ul>n    <li>")   // префикс перед выводимой строкой, узнать префикс getPrefix()
                         ->setSeparator("</li><li>n")   // разделитель между элементами массива, узнать разделитель getSeparator()
                         ->setIndent(4)                 // отступ перед всей строкой (4 пробела), узнать отступ getIndent()
                         ->setPostfix("</li></ul>n");   // постфикс после всей строки, узнать постфикс getPostfix()
  
 /*
  * Вернуть содержимое контейнера $this->items['foo'] вызвав его метод AbstractContainer::__toString(), 
  * который соберёт в строку все элементы контейнера-массива типа ArrayObject с учётом отступа(indent), префикса(prefix), постфикса(postfix) и разделителя(separator):
  * AbstractContainer::__toString(){
  * $items  = $this->getArrayCopy(); // ArrayObject::getArrayCopy() - Создаёт копию ArrayObject в виде массива
  * return $this->getIndent()
  *     . $this->getPrefix()
  *     . implode($this->getSeparator(), $items)
  *     . $this->getPostfix();
  * }
  */
echo $this->placeholder('foo');  // outputs "Some text for later"

/*
 * Зададим отступ(indent), префикс(prefix), постфикс(postfix) и разделитель(separator):
 */
$this->placeholder('foo')->setPrefix("<ul>n    <li>")
                         ->setSeparator("</li><li>n")
                         ->setIndent(4)
                         ->setPostfix("</li></ul>n");

/*
 * Буферизацию вывода активизируется с помощью AbstractContainer::captureStart()
 * Если буферизация(захват) вывода активна, вывод скрипта не высылается (кроме заголовков), а сохраняется во внутреннем буфере.
 * captureStart($type, $key) -  начинает захват. При этом блокируются другие захваты до captureEnd(). Нельзя использовать несколько захватов в одном контейнере заполнителя.
 *      - $type  - одна из констант Placeholder. По умолчанию APPEND.
 *              APPEND - захваченное содержимое добавляется в конец массива контейнера (AbstractContainer::append($bufer))
 *              PREPEND - захваченное содержимое добавляется в начало массива контейнер(AbstractContainer::prepend($bufer))
 *              SET -  заменяется текущее содержимое захваченным. (AbstractContainer::set($bufer))
 *      - $key -  назначает ключ AbstractContainer[$key] для захваченного содержимого. По умолчанию добавляется с числовым ключом в массив ArrayObject
 * captureEnd() -  останавливает захват и помещает содержимое в контейнер объекта в зависимости от настроек captureStart() (APPEND или SET).
 */

// начинаем захват вывода в буфер и делаем установку по умолчанию APPEND для последующего добавления вывода в начало массива контейнера
$this->placeholder('foo')->captureStart();

// захватываемый вывод
foreach ($this->data as $datum): ?>
<div class="foo">
    <h2><?php echo $datum->title ?></h2>
    <p><?php echo $datum->content ?></p>
</div>
<?php endforeach;

// остановить захват вывода: очистить буфер и поместить его в контейнер $this->items['foo']->append()
$this->placeholder('foo')->captureEnd();
// вывести забуфферизованный и сохранённый вывод
echo $this->placeholder('foo');

//...................

// Capture to key
// Задаём для хранения захватываемого вывода ключ в AbstractContainer['data'] и SET - замещение содержимого
$this->placeholder('foo')->captureStart('SET', 'data');

// захватываемый вывод
foreach ($this->data as $datum): ?>
<div class="foo">
    <h2><?php echo $datum->title ?></h2>
    <p><?php echo $datum->content ?></p>
</div>
<?php endforeach;

// остановить захват вывода: очистить буфер и поместить его в контейнер способом SET: $this->items['foo']->set() в свойство-ключ 'data'
$this->placeholder('foo')->captureEnd();
// вывести забуфферизованный и сохранённый вывод из контейнера $this->items['foo'][AbstractContainer['data']]
echo $this->placeholder('foo')->data;
// echo $this->placeholder('foo')['data'];


////////////////////////////////////////////////////////////////////////////////////////////////////
namespace Zend\View\Helper;
use Zend\View\Helper\Placeholder\Container;

class Placeholder extends AbstractHelper{
    /**
     * Placeholder items
     * @var array
     */
    protected $items = array();

    /**
     * Default container class
     * @var string Zend\View\Helper\Placeholder\Container\AbstractContainer
     */
    protected $containerClass = 'Zend\View\Helper\Placeholder\Container';

    /**
     * Placeholder helper
     * @param  string $name
     * @throws InvalidArgumentException
     * @return Placeholder\Container\AbstractContainer
     */
    function __invoke($name = null){
        if ($name == null) {
            throw new InvalidArgumentException('Placeholder: missing argument.  $name is required by placeholder($name)');
        }
        $name = (string) $name;
        return $this->getContainer($name);
    }

    /**
     * createContainer
     * @param  string $key
     * @param  array $value
     * @return Container\AbstractContainer
     */
    function createContainer($key, array $value = array()) {
        $key = (string) $key;

        $this->items[$key] = new $this->containerClass($value);
        return $this->items[$key];
    }

    /**
     * Retrieve a placeholder container
     * @param  string $key
     * @return Container\AbstractContainer
     */
    function getContainer($key) {
        $key = (string) $key;
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }
        $container = $this->createContainer($key);
        return $container;
    }

    /**
     * Does a particular container exist?
     * @param  string $key
     * @return bool
     */
    function containerExists($key) {
        $key = (string) $key;
        $return =  array_key_exists($key, $this->items);
        return $return;
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////

//namespace Zend\View\Helper\Placeholder;
//class Container extends Container\AbstractContainer{}

namespace Zend\View\Helper\Placeholder\Container;

abstract class AbstractContainer extends ArrayObject {
    /**
     * Whether or not to override all contents of placeholder
     * @const string
     */
    const SET = 'SET';

    /**
     * Whether or not to append contents to placeholder
     * @const string
     */
    const APPEND = 'APPEND';

    /**
     * Whether or not to prepend contents to placeholder
     * @const string
     */
    const PREPEND = 'PREPEND';

    /**
     * Key to which to capture content
     * @var string
     */
    protected $captureKey;

    /**
     * Whether or not we're already capturing for this given container
     * @var bool
     */
    protected $captureLock = false;

    /**
     * What type of capture (overwrite (set), append, prepend) to use
     * @var string
     */
    protected $captureType;

    /**
     * What string to use as the indentation of output, this will typically be spaces. Eg: '    '
     * @var string
     */
    protected $indent = '';

    /**
     * What text to append the placeholder with when rendering
     * @var string
     */
    protected $postfix   = '';

    /**
     * What text to prefix the placeholder with when rendering
     * @var string
     */
    protected $prefix    = '';

    /**
     * What string to use between individual items in the placeholder when rendering
     * @var string
     */
    protected $separator = '';

    /**
     * Constructor - This is needed so that we can attach a class member as the ArrayObject container
     */
    public function __construct() {
        parent::__construct(array(), parent::ARRAY_AS_PROPS);
    }

    /**
     * Serialize object to string
     * @return string
     */
    function __toString() {
        return $this->toString();
    }

    /**
     * Render the placeholder
     * @param  null|int|string $indent
     * @return string
     */
    function toString($indent = null) {
        $indent = ($indent !== null)
            ? $this->getWhitespace($indent)
            : $this->getIndent();

        $items  = $this->getArrayCopy();
        $return = $indent
            . $this->getPrefix()
            . implode($this->getSeparator(), $items)
            . $this->getPostfix();
        $return = preg_replace("/(\r\n?|\n)/", '$1' . $indent, $return);

        return $return;
    }

    /**
     * Start capturing content to push into placeholder
     * @param  string $type How to capture content into placeholder; append, prepend, or set
     * @param  mixed  $key  Key to which to capture content
     * @throws Exception\RuntimeException if nested captures detected
     * @return void
     */
    public function captureStart($type = AbstractContainer::APPEND, $key = null) {
        if ($this->captureLock) {
            throw new Exception\RuntimeException(
                'Cannot nest placeholder captures for the same placeholder'
            );
        }
        $this->captureLock = true;
        $this->captureType = $type;
        if ((null !== $key) && is_scalar($key)) {
            $this->captureKey = (string) $key;
        }
        ob_start();
    }

    /**
     * End content capture
     * @return void
     */
    function captureEnd() {
        $data               = ob_get_clean();
        $key                = null;
        $this->captureLock = false;
        if (null !== $this->captureKey) {
            $key = $this->captureKey;
        }
        switch ($this->captureType) {
            case self::SET:
                if (null !== $key) {
                    $this[$key] = $data;
                } else {
                    $this->exchangeArray(array($data));
                }
                break;
            case self::PREPEND:
                if (null !== $key) {
                    $array  = array($key => $data);
                    $values = $this->getArrayCopy();
                    $final  = $array + $values;
                    $this->exchangeArray($final);
                } else {
                    $this->prepend($data);
                }
                break;
            case self::APPEND:
            default:
                if (null !== $key) {
                    if (empty($this[$key])) {
                        $this[$key] = $data;
                    } else {
                        $this[$key] .= $data;
                    }
                } else {
                    $this[$this->nextIndex()] = $data;
                }
                break;
        }
    }

    /**
     * Get keys
     * @return array
     */
    function getKeys() {
        $array = $this->getArrayCopy();
        return array_keys($array);
    }

    /**
     * Retrieve container value
     * If single element registered, returns that element; otherwise,
     * serializes to array.
     * @return mixed
     */
    function getValue() {
        if (1 == count($this)) {
            $keys = $this->getKeys();
            $key  = array_shift($keys);
            return $this[$key];
        }
        return $this->getArrayCopy();
    }

    /**
     * Retrieve whitespace representation of $indent
     * @param  int|string $indent
     * @return string
     */
    function getWhitespace($indent) {
        if (is_int($indent)) {
            $indent = str_repeat(' ', $indent);
        }
        return (string) $indent;
    }

    /**
     * Set a single value
     * @param  mixed $value
     * @return void
     */
    function set($value) {
        $this->exchangeArray(array($value));
        return $this;
    }

    /**
     * Prepend a value to the top of the container
     * @param  mixed $value
     * @return self
     */
    function prepend($value) {
        $values = $this->getArrayCopy();
        array_unshift($values, $value);
        $this->exchangeArray($values);
        return $this;
    }

    /**
     * Append a value to the end of the container
     * @param  mixed $value
     * @return self
     */
    function append($value) {
        parent::append($value);
        return $this;
    }

    /**
     * Next Index as defined by the PHP manual
     * @return int
     */
    function nextIndex() {
        $keys = $this->getKeys();
        if (0 == count($keys)) {
            return 0;
        }
        return $nextIndex = max($keys) + 1;
    }

    /**
     * Set the indentation string for __toString() serialization,
     * optionally, if a number is passed, it will be the number of spaces
     * @param  string|int $indent
     * @return self
     */
    function setIndent($indent) {
        $this->indent = $this->getWhitespace($indent);
        return $this;
    }

    /**
     * Retrieve indentation
     * @return string
     */
    function getIndent(){
        return $this->indent;
    }

    /**
     * Set postfix for __toString() serialization
     * @param  string $postfix
     * @return self
     */
    function setPostfix($postfix) {
        $this->postfix = (string) $postfix;
        return $this;
    }

    /**
     * Retrieve postfix
     * @return string
     */
    function getPostfix() {
        return $this->postfix;
    }

    /**
     * Set prefix for __toString() serialization
     * @param  string $prefix
     * @return self
     */
    function setPrefix($prefix) {
        $this->prefix = (string) $prefix;
        return $this;
    }

    /**
     * Retrieve prefix
     * @return string
     */
    function getPrefix() {
        return $this->prefix;
    }

    /**
     * Set separator for __toString() serialization
     * Used to implode elements in container
     * @param  string $separator
     * @return self
     */
    public function setSeparator($separator) {
        $this->separator = (string) $separator;
        return $this;
    }

    /**
     * Retrieve separator
     * @return string
     */
    public function getSeparator() {
        return $this->separator;
    }
}
