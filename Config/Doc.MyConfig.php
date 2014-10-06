<?php
/*
 * в module.config.php конкретного модуля или модуля всего приложения Application
 * можно прописать свою какую-то конфигурацию ('my_config') для какого-то сервиса('myclass'):
 */

'my_config' => array(
    // Конфигурация хелпера flashmessenger() (настройки html-шаблона сообщения)
    'myclass' => array(
        'config1'      => '<div%s><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><ul><li>',
        'config2'     => '</li></ul></div>',
        'config3' => '</li><li>'
    )
),

/*
 * Для конфигурирования объектов, надо их создавать через фабрику реализующую FactoryInterface,
 * регистрировать ей в конфиге сервис-менеджера и вызывать её как сервис.
 * В фабрике реализовать вызов соответствующей конфигурации класса и вернуть сконфигурированный объект
 */
class MyClassFactory implements FactoryInterface {
    
    function createService( ServiceLocatorInterface $serviceLocator ) {
        
        $serviceLocator = $serviceLocator->getServiceLocator();                
        $config = $serviceLocator->get('Config');
        
        if ( isset($config['my_config']['myclass'] )) {
            $myConfig = $config['my_config']['myclass'];
            
            if (isset($myConfig['config1'])) {
                $myConfig1 = $myConfig['config1'];
            }
            if (isset($myConfig['config2'])) {
                $myConfig2 = $myConfig['config2'];
            }
            if (isset($myConfig['config3'])) {
                $myConfig3 = $myConfig['config3'];
            }
        }
        return new MyClass( $myConfig1, $myConfig2, $myConfig3 );
    }
}




/*
 * Пример реализации конфигурирования хелпера flashMessenger() через фабрику Zend\View\Helper\Service\FlashMessengerFactory:
 */
// в конфиге прописать
'view_helper_config' => array(
    'flashmessenger' => array(
        'message_open_format'      => '<div%s><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><ul><li>',
        'message_close_string'     => '</li></ul></div>',
        'message_separator_string' => '</li><li>'
    )
)
    
// фабрика 
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