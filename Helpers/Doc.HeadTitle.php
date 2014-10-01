<?php

// Первый способ добавления заголовка в хранилище - через __invoke()
// AbstractContainer::set("заголовок") второй аргумент 'SET'
$this->headtitle('Заголовок страницы SET', 'SET');
// AbstractContainer::append("заголовок") второй аргумент по умолчанию - 'APPEND'
$this->headtitle('Заголовок страницы APPEND');
// AbstractContainer::prepend("заголовок") второй аргумент 'PREPEND'
$this->headtitle('Заголовок страницы PREPEND', 'PREPEND');
echo $this->headtitle()."<br />\n"; 


// Другой способ добавления заголовка в хранилище - через методы AbstractContainer
$title = $this->headtitle();
$title->set('Заголовок через set()')
	->append('Заголовок через append()')
	->prepend('Заголовок через prepend()');
echo $title;

// зададим отступ, префикс, разделитель и постфикс
$this->headtitle()
	->setIndent(4)	
	->setPrefix("!!!!!")
	->setSeparator("---")	
	->setPostfix("?????");
echo $this->headtitle();
  
// вывести заголовок без тегов <title>
echo $this->headTitle()->renderTitle()."<br />\n";

// сохраняем заголовки в свойствах(элементах массива) контейнера хелпера
$this->headtitle()->bar1 = 'Заголовок через свойство';
$this->headtitle()['bar2'] = 'Заголовок через ячейки массива';
$this->headtitle()->offsetSet('bar3', 'Заголовок через offsetSet()');
echo $this->headTitle()->renderTitle()."<br />\n";

// проверка существования заголовка
if ( isset($this->headtitle()->bar1) ) echo 'Контейнер заголовка bar1 существует'."<br />\n";
if ( $this->headtitle()->offsetExists('bar2') ) echo 'Контейнер заголовка bar2 существует'."<br />\n";


