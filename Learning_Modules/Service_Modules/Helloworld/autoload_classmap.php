<?php
/*
 * autoload_classmap.php возвращает массив PHP, отображающий имена классов к именам
 * файлов, чтобы любой произвольный автозагрузчик, а не только тот, что
 * в Zend Framework – но и, например, в composer, мог обрабатывать его и, при
 * необходимости, комбинировать с картами классов других библиотек.
 */
return array(
    // 'полное имя класса' => 'абсолютный путь к файлу с классом'
    'Helloworld\Module' => __DIR__.'/Module.php',
    'Helloworld\Controller\IndexController' => __DIR__.'/src/Helloworld/Controller/IndexController.php',
);