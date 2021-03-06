<?php
/*
basePath($file=null) - возвращает базовый адрес сайта
    $file - путь к файлу, добавляется к базовому адресу сайта
Если используются базовые пути ZF2 MVC, то basePath() будет ссылать на папку public фреймворка,
от которой и ведёт путь $file.
Можно принудительно изменить basePath с помощью $this->plugun('basePath')->setBasePath('myfolder'),
но тогда изменятся все пути на основе basePath. 

// пример подключения стилей и фавикона с использованием $this->basePath() из дефолтной страницы фреймворка
echo $this->headLink(array('rel' => 'shortcut icon', 'type' => 'image/vnd.microsoft.icon', 'href' => $this->basePath() . '/img/favicon.ico'))
                ->prependStylesheet($this->basePath() . '/css/style.css')
                ->prependStylesheet($this->basePath() . '/css/bootstrap-theme.min.css')
                ->prependStylesheet($this->basePath() . '/css/bootstrap.min.css');

// пример подключения скриптов 
echo $this->headScript() с использованием $this->basePath() из дефолтной страницы фреймворка
    ->prependFile($this->basePath() . '/js/bootstrap.min.js')
    ->prependFile($this->basePath() . '/js/jquery.min.js')
    ->prependFile($this->basePath() . '/js/respond.min.js', 'text/javascript', array('conditional' => 'lt IE 9',))
    ->prependFile($this->basePath() . '/js/html5shiv.js',   'text/javascript', array('conditional' => 'lt IE 9',)); 

*/

$this->headLink()->appendStylesheet($this->basePath() . '/css/mystyle.css');