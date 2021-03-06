<?php
/*
 * Хелпер serverUrl() возвращает строку полного (серверного) имени урла
 * Параметром зелпера должна быть строка роута, прикрепляемая к серверному урлу
 * Для получения серверного имени хелпер использует $_SERVER['REQUEST_URI']
 */
// http://zend.loc
echo $this->serverUrl()."<br>\n";
// http://zend.loc/helpers/index/info
echo $this->serverUrl('/helpers/index/info')."<br>\n";

/*
 * Для получениия полного серверного урл роута приложения, прописанного в конфиге,
 * надо в качестве параметра хелпера использовать хелпер url(), который возвращает строку урла роута
 */
// http://zend.loc/helpers/index/info/2
echo $this->serverUrl($this->url('helpers-index', ['action'=>'info', 'id'=>2]))."<br>\n";

/*
 * Для получения подробной информации серверного урла надо сначала вызвать объект хелпера 
 * (__invoke() возвращает строку, поэтому объект хелпера надо создавать через plugin()),
 * а затем вызвать спец. метод
 */
// Scheme: Использует $_SERVER['HTTP_SCHEME'] и другие константы для получения имени схемы (возвращает http or https)
echo $this->plugin('serverUrl')->getScheme()."<br>\n"; // http
// Задать новую схему (http or https) 
$this->plugin('serverUrl')->setScheme('https');

// Port: использует $_SERVER['SERVER_PORT'] и другие константы для получения имени порта
echo $this->plugin('serverUrl')->getPort()."<br>\n"; // 80
// Задать другой порт
$this->plugin('serverUrl')->setPort(80);

// Host: использует  $_SERVER['HTTP_HOST'] и другие константы для получения имени хоста
echo $this->plugin('serverUrl')->getHost()."<br>\n"; // zend.loc
// Задать другой хост
$this->plugin('serverUrl')->setHost('zend.loc');

