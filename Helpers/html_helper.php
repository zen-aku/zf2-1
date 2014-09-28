<?php
/*
 * Визуальное сравнение кодов на чистом php и смешанном html-php
 */
// Создание классов на лету
// Очень красивый, логичный, компактный, быстрый, понятный, удобный в разработке и отладке вариант и отлично вписывается в концепцию ООП!!!
(new Div)->setClass("collapse navbar-collapse")->open();
    (new Ul)->setClass("nav navbar-nav")->open();
        (new Li)->setId($this->newcolor)->open();
            (new A)->setClass("nav-href")->setHref($this->url('home'))->open();
                echo $this->translate('Home');
            A::Close(); 
        Li::Close();  
    Ul::Close();
Div::Close();
?>

<div class="collapse navbar-collapse">
    <ul class="nav navbar-nav">
        <li id = "<?php echo $this->newcolor?>">
            <a class="nav-href" href="<?php echo $this->url('home') ?>">
                <?php echo $this->translate('Home') ?>
            </a>
        </li>
    </ul>
</div>

<?php 
// отдельные функции открывающего и разделяющего тегов и открывающий формируется вызовом метода open()
div()->setClass("collapse navbar-collapse")->open();
    ul()->setClass("nav navbar-nav")->open();
        li()->setId($this->newcolor)->open();
            a()->setClass("nav-href")->setHref($this->url('home'))->open();
                echo $this->translate('Home');
            aClose(); 
        liClose();  
    ulClose();
divClose();
?>


<?php
/*
 * Варианты реализации html-кода на php
 */
/*
 * Как хелперы (используя сервис-локатор как хранилище) 
 * Много минусов:
 *  - громоздкость вызова, тормознутость, сложность отладки
 */
$this->div()->setClass("collapse navbar-collapse")->openTeg();
    $this->ul()->setClass("nav navbar-nav")->openTeg();
        $this->li()->setId($this->newcolor)->openTeg();
            $this->a()->setClass("nav-href")->setHref($this->url('home'))->openTeg();
                echo $this->translate('Home');
            $this->a()->closeTeg(); 
        $this->li()->closeTeg();  
    $this->ul()->closeTeg();
$this->div()->closeTeg();

/*
 * Как функции: компактность, простота
 * Минус: не вписывается в ООП-модель ZF2
 * Этот вариант уступает в 2 раза по скорости стандартному созданию объектов через new
 */
function div($param=null, $teg) {
    if (!$teg)
        return new Zend\путь\Div($param);
    else 
        echo '</div>';     
}

class Div {   
     private $class;
     function __construct($param) { }
     
     function setClass($class, $teg=null) {
        $this->class = $class;
        if (!$teg) return $this();
        return $this->openTeg();
     }
      
     function openTeg() {
        // вывести собранный тег
        echo '';
     }
     
}

// открывающий тег формируется по параметру метода (1)
div()->setClass("collapse navbar-collapse", 1);
    ul()->setClass("nav navbar-nav", 1);
        li()->setId($this->newcolor, 1);
            a()->setClass("nav-href")->setHref($this->url('home'), 1);
                echo $this->translate('Home');
            a(1); 
        li(1);  
    ul(1);
div(1);

// открывающий тег формируется вызовом метода openTeg();
div()->setClass("collapse navbar-collapse")->openTeg();
    ul()->setClass("nav navbar-nav")->openTeg();
        li()->setId($this->newcolor)->openTeg();
            a()->setClass("nav-href")->setHref($this->url('home'))->openTeg();
                echo $this->translate('Home');
            a(1); 
        li(1);  
    ul(1);
div(1);

// открывающий и закрывающий теги формируются отдельными функциями
function divOpen($param=null) {
    return new Zend\путь\Div($param);     
}
function divClose() {
    echo '</div>';
}

divOpen()->setClass("collapse navbar-collapse", 1);
    ulOpen()->setClass("nav navbar-nav", 1);
        liOpen()->setId($this->newcolor, 1);
            aOpen()->setClass("nav-href")->setHref($this->url('home'), 1);
                echo $this->translate('Home');
            aClose(); 
        liClose();  
    ulClose();
divClose();

// Самый логичный и изящный вариант: отдельные функции открывающего и разделяющего тегов и открывающий тег формируется вызовом метода open()
div()->setClass("collapse navbar-collapse")->open();
    ul()->setClass("nav navbar-nav")->open();
        li()->setId($this->newcolor)->open();
            a()->setClass("nav-href")->setHref($this->url('home'))->open();
                echo $this->translate('Home');
            aClose(); 
        liClose();  
    ulClose();
divClose();

/*
 * Созданием объектов на лету: удобство для подсказки методов для разработки и отладки в среде разработки и скорость
 * Минусы: как вызвать неймспейсы для всех классов тегов? Вызвать с помощью requie и своего автолоадера классов?
 * Отлично вписывается в ООП-модель ZF2!!! Есть подсветка при выделении классов открывающего и закрывающего тегов!!!
 * Очень красивый, логичный, компактный, быстрый, понятный, удобный в разработке и отладке вариант и отлично вписывается в концепцию ООП!!!
 * В NetBeans можно настроить шаблон, который 
 */
(new Div)->setClass("collapse navbar-collapse")->open();
    (new Ul)->setClass("nav navbar-nav")->open();
        (new Li)->setId($this->newcolor)->open();
            (new A)->setClass("nav-href")->setHref($this->url('home'))->open();
                echo $this->translate('Home');
            A::Close(); 
        Li::Close();  
    Ul::Close();
Div::Close();
/*
 * В NetBeans можно настроить шаблоны, которые значительно ускорят вёрстку html-php кода!!!
 * При наборе newdiv будет генериться код (аналог html: <div></div>):
 */
(new Div)->open();

Div::Close();

/**
 * Реализация класса тега div
 */
class Div { 
    
    private $id="";
            
    function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    function getId() {
        return $this->id;
    }
        
    function openTeg() {
        echo 
            "<div id = {$this->id}>";
    }
     static function close() {
       echo '</div>'; 
    }    
}

/*
 * Вариант с синглтогом
 * Div::teg() возвращает объект-клон Div. 
 * При первом обращении к Div::get() создаётся объект Div (и возвращается клон) и сохраняется в свойстве Div.
 * При последующих обращениях к Div::get() возвращается клон объекта из свойства Div.  
 * Этот вариант уступает в 2 раза по скорости стандартному созданию объектов через new
 */
Div::teg()->setClass("collapse navbar-collapse")->open();
    Ul::teg()->setClass("nav navbar-nav")->open();
        Li::teg()->setId($this->newcolor)->open();
            A::teg()->setClass("nav-href")->setHref($this->url('home'))->open();
                echo $this->translate('Home');
            A::Close(); 
        Li::Close();  
    Ul::Close();
Div::Close();


/*
 * Нужно ли клонировать?
 * По идее да, потому что нужно создавать каждый раз новый объект, а не использовать один и тот же.
 */
class Div { 
    
    private $id = "";
    private static $instance = null;       // Div
    
    private function __construct(){}    
    static function teg() {
        if ( is_null(self::$instance) ) 
            self::$instance = new Div();
        return clone self::$_instance;
    }  
    static function close() {
       echo '</div>'; 
       //unset($this);  //???
    }  
    function setId($id) {
        $this->id = $id;
        return $this;
    }   
    function getId() {
        return $this->id;
    }      
    function openTeg() {
        echo "<div id = {$this->id}>";
    }   
}

/*
 * Клонирование объектов-тегов из хранилища.
 * Громоздкий код, но по скорости аналогичен стандартному способу создания объекта через new 
 */
$html = new Html();

$html->div()->setClass("collapse navbar-collapse")->open();
    $html->ui()->setClass("nav navbar-nav")->open();
        $html->li()->setId($this->newcolor)->open();
            $html->a()->setClass("nav-href")->setHref($this->url('home'))->open();
                echo $this->translate('Home');
            A::Close(); 
        Li::Close();  
    Ul::Close();
Div::Close();
/*
 * Хранилище прототипов
 */
class Html {
    
    private $div;
    private $ul;
    
    function __construct() {
        $this->div = new Div();
        $this->ul = new Ul();
    }
    
    function div() {
       return clone $this->div; 
    }
    function ul() {
       return clone $this->ul; 
    }
    
}