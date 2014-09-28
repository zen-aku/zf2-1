<?php

class IndexController extends AbstractActionController {
    function indexAction() {
	
        /*
         * Params предоставляет доступ к различным данным из запроса в контроллере
         * Zend\Mvc\Controller\Plugin\Params::__invoke($param = null, $default = null)
         * Если $param == null, возвращается объект класса Params
         * Если $param != null, возвращает метод Params::fromRoute($param, $default)
         */
        $this->params();
        
        /*
         * Params::__invoke()::fromPost($param = null, $default = null)
         * Получить параметры из POST.
         * $param - имя возвращаемого параметра в массиве POST. 
         * Если $param == null, возвращается ассоц массив всех параметров из POST
         */
        $login = $this->params()->fromPost('loogin');
        
        /*
         * Params::__invoke()::fromQuerry($param = null, $default = null)
         * Получить параметры из GET.
         * $param - имя возвращаемого параметра в массиве GET.
         * Если $param == null, возвращается ассоц массив всех параметров из GET
         */
        $user = $this->params()->fromQuery('name');
        
        /*
         * Params::__invoke()::fromHeader($param = null, $default = null)
         * Получить параметры из заголовка запроса
         * $param - имя возвращаемого параметра в массиве HEADER(?).
         * Если $param == null, возвращается ассоц массив всех параметров из HEADER
         */
        $header = $this->params()->fromHeader();
        
        /*
         * Params::__invoke()::fromFiles($name = null, $default = null)
         * Получить файл по имени из запроса
         * $param - имя возвращаемого файла в массиве передаваемых файлов в запросе.
         * Если $param == null, возвращается ассоц массив всех передаваемых файлов в запросе
         */
        $file = $this->params()->fromFiles('filename');
        
        /*
         * Params::__invoke()::fromRoute($param = null, $default = null)
         * Получить параметр из Route (из роута, по которому было обращение к данному контроллеру)
         * $param - имя параметра роута (параметры роута в конфиге?)
         * Если $param == null, возвращается ассоц массив всех параметров роута, по которому был осуществлён запрос
         */
        $route = $this->params()->fromRoute('param');
              
    }

}