<?php

return array(
    // 'полное имя класса' => 'абсолютный путь к файлу с классом'
    'HtmlGenerator\Module' => __DIR__.'/Module.php',
	
	// controllers
    'HtmlGenerator\Controller\IndexController'  => __DIR__.'/src/HtmlGenerator/Controller/IndexController.php',
	
	// services
	
	// helpers
	
    // classes
    'HtmlGenerator\View\Helper\Html'            =>  __DIR__.'/view/helper/Html.php',
	'HtmlGenerator\View\Helper\HtmlHelper'      =>  __DIR__.'/view/helper/HtmlHelper.php',
	'HtmlGenerator\View\Helper\AbstrHtmlElement'=>  __DIR__.'/view/helper/AbstrHtmlElement.php',
    'HtmlGenerator\View\Helper\A'               =>  __DIR__.'/view/helper/A.php',
    'HtmlGenerator\View\Helper\Span'            =>  __DIR__.'/view/helper/Span.php',
    'HtmlGenerator\View\Helper\Br'              =>  __DIR__.'/view/helper/Br.php',
    
);