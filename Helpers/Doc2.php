
		Краткий список хелперов:
		------------------------
doctype()	- задать doctype html-странице. Есть функции проверки доктайпа.
	Имеет смысл, если в коде используется проверка доктайпа html-страницы. 
	
basePath() - возвращает базовый адрес фреймворка (по умолчанию директория корневой папки public)

cycle() - бесконечный итератор. Полезный хелпер.

gravatar() - представляет собой сервис, с помощью которого можно подключить на страницу
			аватар, зарегистрированный пользователем на http://www.gravatar.com
			
escapeHtml() - экранирует спец символы в html-контексте
escapeHtmlAttr() - экранирует спец символы в контексте аттрибутов html-элементов
escapeJs() - экранирует спец символы в контексте js-кода
escapeCss() - экранирует спец символы в контексте Css-кода
escapeUrl() - экранирует спец символы в контексте Url 
	
placeholder() - используется для сохранения содержимого между скриптом вида и отображением в специальном контейнере на базе Zend\View\Helper\Placeholder\Container\AbstractContainer.
	Полезный хелпер, позволяет собирать массив контента в отформатированную по заданным правилам строку 
	(отступ, префикс, разделитель, постфикс). Например таким образом можно формировать теги списков, таблиц и т.д.
	Также имеется возмодность буфферизировать вывод и выводить его в нужный момент. 
	placeholder универсален и на базе его можно создавать различные генераторы кода.	
headTitle() - Генерирует заголовок <title> в блоке <head>. Это частный случай хелпера placeholder(). 			
headStyle() - Генерирует inline стили <style>. Это частный случай хелпера placeholder(). 	
headLink() - Генерирует ссылки <link> в блоке <head>. Это частный случай хелпера placeholder().	
headMeta() - Генерирует тег <meta> в блоке <head>. Это частный случай хелпера placeholder().
headScript() - Генерирует тег <script> в блоке <head>. Это частный случай хелпера placeholder().
inlineScript() - клон headScript(), генерирует тег <script> в блоке <body>. Это частный случай хелпера placeholder(). Можно убрать из фреймворка.

htmlList() - Генерирует html-список ol/li из массива данных.
htmlObject() - Генерирует тег <object> для любых типов объектов
htmlFlash() - Генерирует тег <object> для встраивания флэш-файлов. Он расширяет хелпер htmlObject()
htmlPage() - Генерирует тег <object> для встраивания других html страниц. Он расширяет хелпер htmlObject()
htmlQuicktime() - Генерирует тег <object> для встраивания Quicktime файлов. Он расширяет хелпер htmlObject(). Можно убрать из фреймворка.



		Как и какие хелперы можно убрать из фреймворка:
		------------------------------------------------

Чтобы убрать встроенный хелпер из фреймворка, надо из плагин-менеджера Zend\View\HelperPluginManager
убрать соответствующую запись из свойства $invokableClasses. 
Некоторый хелперы, напр. doctype() или escape...() используются в других сервисах(хелперах или плагинах)
и поэтому их изъятие невозможно без изменения кода в связанных сним классах. Поэтому перед удалением хелпера
через поиск в netBeans проверить использование хелпера в фреймворке и только потом принимать решение об его удалении.
Кроме того надо учесть возможность применения хелперов в сторонних модулях, напр. inlineScript(), который по сути бесполезен,
но он может широко использоваться в сторонних модулях.

Кандидаты на удаление:
 - htmlQuicktime() - можно смело удалять, данный формат уже редкоиспользуется
 - htmlPage() - можно смело удалять, он расширяет простой htmlObject(), встраивание другой html-страницы в тело текущей крайне редко используется 
				и в случае необходимости можно воспользоваться htmlObject()
 
 - gravatar() -	очень специализированный сервис, который можно вынести из корня фреймворка в тот модуль, где он будет использоваться			
 - inlineScript() - он отличается от headScript() только написанием, т.е. симантикой. Он может ипользоваться в сторонних модулях или встроенных примерах, поэтому удаление может вызвать в будущем затруднения(?)
 - htmlFlash() - он расширяет простой htmlObject(), используется редко, поэтому для flash-файлов можно использовать простой htmlObject()