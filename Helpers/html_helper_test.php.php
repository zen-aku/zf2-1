<?php

class Div { 
    
    private $id = "";

    private static $_instance = null;        // Div
    
    public function __construct(){}			// public - для теста
    static function teg() {
        if ( is_null(self::$_instance) ) 
            self::$_instance = new Div();	
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

function div() {
    return new Div();     
}

class Html {
    
    private $div;
    
    function __construct() {
        $this->div = new Div();
    }
    
    function div() {
       return clone $this->div;    
	}
}

$html = new Html();


set_time_limit(120); 

function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


$time_start = microtime_float();

for ( $i = 0; $i < 1000000; $i++) {
	// 1. Создать объект с помощью функции. Время 9.9с
	div();
	
	// 2.Создать объект стандартным способом. Время 5.4c
	//new Div();
	
	// 3. Создать объект клонированием с синглтона. Время 9.9c
	//Div::teg();
	
	// 4. Создать объект клонированием с хранилища прототипов. Время 5.6c 
	//$html->div();
}

$time_end = microtime_float();
$time = round($time_end - $time_start, 5);
// продолжительность скрипта
echo "time_script = $time sec<br>";

/*
 * Вывод:
 *	- Самый быстрый способ создания объектов - стандартный через new.
 *  - Сравнимо по скорости со стандартным способом - создание объектов из хранилища прототипов
 *  - Значительно медленнее создание объекта из функции и из синглтона.
 * 
 */