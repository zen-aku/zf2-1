<?php
/*
 * autoload_function.php возвращает PHP - функцию, которая может быть обработана автозагрузчиком,
* например, в виде дополнительного источника для автозагрузки классов.
* В конечном счете, эта функция также снова получает доступ к карте классов autoload_classmap.php'.
* Это третий путь автозагрузки классов вместе с autoload_classmap и StandardAutoloader
* Требует подключение регистрации функции автозагрузки (autoload_register.php) в коде файла:
*      require_once '.../autoload_register.php';
*/
return function ($class) {
    static $map;
    if (!$map) {
        $map = include __DIR__ . '/autoload_classmap.php';
    }

    if (!isset($map[$class])) {
        return false;
    }
    return include $map[$class];
};
