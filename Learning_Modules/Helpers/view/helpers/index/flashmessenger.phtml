<?php
/*
 * Хелпер flashMessenger($namespace) при обращении к нему вызывает одноимённый плагин контроллера flashMessenger(),
 * с помощью которого генерировались сообщения из экшена контроллера, и возвращает массив сообщений, который
 * хранится в переданном ключе очереди $namespace (ключе SplQueue->$namespace).
 * Если не передавать $namespace, то __invoke() возвращает объект хелпера и для его отображения 
 * с определёнными стилями, экранированием, форматированием надо использовать метод
 *
 * renderMessages($namespace = PluginFlashMessenger::NAMESPACE_DEFAULT, array $classes = array())
 *      $namespace - одно из дефолтных имён ключа хранилища сообщений: 
 *          'default' или Zend\Mvc\Controller\Plugin\FlashMessenger::NAMESPACE_DEFAULT
 *          'info' или Zend\Mvc\Controller\Plugin\FlashMessenger::NAMESPACE_INFO
 *          'error' или FlashMessenger::NAMESPACE_ERROR 
 *          'success' или FlashMessenger::NAMESPACE_SUCCESS
 *          'warning' или FlashMessenger::NAMESPACE_WARNING
 *          или любое своё имя, использованное в плагине контроллера
 *      $classes - мвссив имён классов CSS для вывода сообщения
 * 
 * 
 */
echo $this->flashMessenger()->render();
echo $this->flashMessenger()->render('info');
echo $this->flashMessenger()->render('error');
echo $this->flashMessenger()->render('success');
echo $this->flashMessenger()->render('warning');
echo $this->flashMessenger()->render('mynamespace');

// class = "alert  alert-danger"
echo $this->flashMessenger()->render('error',  array('alert', 'alert-danger'));

// отобразить текушее сообщение (см. разницу с просто сообщением в Plugins/Doc.FlashMessenger)
echo $this->flashMessenger()->renderCurrent('error',  array('alert'));


/*
 * Метод __call позволяет вызвать из хелпера любой метод плагина контроллера flashMessenger():
 * __call($method, $argv) {
 *      $flashMessenger = $this->getPluginFlashMessenger();
 *       return call_user_func_array(array($flashMessenger, $method), $argv);
 *   }
 * Кроме того, можно вызвать плагин контроллера методом и вызвать его метод:
 *  $this->flashMessenger()->getPluginFlashMessenger()->addMessage('Сообщение');
 */
$this->flashMessenger()->addMessage('Создаём сообщение из вида вызовом метода addMessage() плагина flashMessenger()');
// отобразится при повторном обновлении страницы
echo $this->flashMessenger()->render();


/*
 * html-шаблон сообщения можно задать с помощью методов сеттеров (у них же есть и соответствующие геттеры), 
 * которые изменяют соответствующие свойства хелпера:
 *  setMessageCloseString($messageCloseString) - открывающая строка сообщения
 *  setMessageOpenFormat($messageOpenFormat) - закрывающая строка сообщения
 *  setMessageSeparatorString($messageSeparatorString) - разделитель сообщений 
 *  %s - заполнится соответствующим классом CSS элемента
 * По умолчанию сообщение имеет шаблон: 
 */
/*  //Templates for the open/close/separators for message tags 
    protected $messageCloseString     = '</li></ul>';
    protected $messageOpenFormat      = '<ul%s><li>';
    protected $messageSeparatorString = '</li><li>';
*/
echo  $this->flashMessenger() 
    ->setMessageOpenFormat( '<div%s><p>' ) 
    ->setMessageSeparatorString( '</p><p>' ) 
    ->setMessageCloseString( '</p></div>' ) 
    ->render( 'success' );

/*
 * Пример вывода отформатированного сообщения с шаблоном http://getbootstrap.com/ и кнопкой закрытия сообщения
 */
 $flash = $this->flashMessenger();
 $flash->setMessageOpenFormat('<div%s>
     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
         &times;
     </button>
     <ul><li>')
     ->setMessageSeparatorString('</li><li>')
     ->setMessageCloseString('</li></ul></div>');

 echo $flash->render('error',   array('alert', 'alert-dismissable', 'alert-danger'));
 echo $flash->render('info',    array('alert', 'alert-dismissable', 'alert-info'));
 echo $flash->render('default', array('alert', 'alert-dismissable', 'alert-warning'));
 echo $flash->render('success', array('alert', 'alert-dismissable', 'alert-success'));
 
/*
 * Объект Хелпера flashMessenger() создаётся хелперменеджером через вызов фабрики Zend\View\Helper\Service\FlashMessengerFactory,
 * которая считывает настройки из конфига из ключа массива config['view_helper_config']['flashmessenger']
 * и передаёт их соответствующим методам создания html-шаблона сообщения (setMessageOpenFormat(), setMessageSeparatorString(), setMessageCloseString())
 */ 
/*
class FlashMessengerFactory implements FactoryInterface {
    function createService(ServiceLocatorInterface $serviceLocator){
        $serviceLocator = $serviceLocator->getServiceLocator();
        $helper = new FlashMessenger();
        $controllerPluginManager = $serviceLocator->get('ControllerPluginManager');
        $flashMessenger = $controllerPluginManager->get('flashmessenger');
        $helper->setPluginFlashMessenger($flashMessenger);
        $config = $serviceLocator->get('Config');
        if (isset($config['view_helper_config']['flashmessenger'])) {
            $configHelper = $config['view_helper_config']['flashmessenger'];
            if (isset($configHelper['message_open_format'])) {
                $helper->setMessageOpenFormat($configHelper['message_open_format']);
            }
            if (isset($configHelper['message_separator_string'])) {
                $helper->setMessageSeparatorString($configHelper['message_separator_string']);
            }
            if (isset($configHelper['message_close_string'])) {
                $helper->setMessageCloseString($configHelper['message_close_string']);
            }
        }
        return $helper;
    }
}
*/ 
/*
 * Поэтому в module.config.php конкретного модуля или модуля всего приложения Application
 *  можно прописать конфигурацию html-шаблона сообщения:
 */ 
/*
'view_helper_config' => array(
    'flashmessenger' => array(
        'message_open_format'      => '<div%s><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><ul><li>',
        'message_close_string'     => '</li></ul></div>',
        'message_separator_string' => '</li><li>'
    )
)
*/   

 
/*
 * class FlashMessenger implements ServiceLocatorAwareInterface, поэтому при необходимости получения доступа
 * из вида к сервис-менеджеру можно использовать объект хелпера flashMessenger():
 *      $this->flashMessenger()->getServiceLocator()->get('сервис');
 * Поискать более изящный способ получения сервисменеджера из вида
 */

