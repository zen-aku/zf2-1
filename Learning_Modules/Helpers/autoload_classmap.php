<?php

return array(
    // 'полное имя класса' => 'абсолютный путь к файлу с классом'
    'Helpers\Module' => __DIR__.'/Module.php',
	
	// controllers
    'Helpers\Controller\IndexController' => __DIR__.'/src/Helpers/Controller/IndexController.php',
	
	// services
	
	// helpers
	
    // classes
    'Helpers\View\Helper\Html' =>  __DIR__.'/view/helper/Html.php',
    'Helpers\View\Helper\HtmlCommander' =>  __DIR__.'/view/helper/HtmlCommander.php',
    
	'Helpers\View\Helper\AbstrHtmlElement' =>  __DIR__.'/view/helper/AbstrHtmlElement.php',
    'Helpers\View\Helper\A' =>  __DIR__.'/view/helper/A.php',
    'Helpers\View\Helper\Span' =>  __DIR__.'/view/helper/Span.php',
    'Helpers\View\Helper\Br' =>  __DIR__.'/view/helper/Br.php',
    
);