<?php

class IndexController extends AbstractActionController {
    function indexAction() {
		/*
         * Плагин Zend\Mvc\Controller\Plugin\Forward позволяет внутри контроллера выполнить вызов другого контроллера.
         * Плагин никуда никого не перенаправляет, вместо этого он "выполняет " другой контроллер.
         * С плагином Forward выполняется только метод контроллера dispatch(), и он возвращает, 
         * как это обычно бывает, когда контроллер выполнен, объект типа ViewModel в качестве результата.
         */
        // Возвращает объект класса Forward
        $this->forward(); 
        
        /*
         * Zend\Mvc\Controller\Plugin\Layout::dispatch($name, array $params = null)
		 *		$params - массив параметров роута из конфига: ['action' => 'info', 'id' => 5]
		 *				если не задан, то берётся дефолтный экшен из конфига (как правило это index)
         * Запустить контроллер: вызывается метод dispatch() контроллера $name с роутингом, определённым параметром $params
         */
        $resultDispatch = $this->forward()->dispatch('Helloworld\Controller\Other');
              
        // Если мы хотим имитировать "перенаправление контроллера"
        return $this->forward()->dispatch('Helloworld\Controller\Other');
            
        // Если мы хотим вызвать определенное действие другого контроллера, это можно сделать следующим образом:
        return $this->forward()->dispatch(
            'Helloworld\Controller\Other',
            array('action' => 'test')      
        );
           
    }
    
    /**
     * Генерация модели представления одного контроллера в модели представления другого контроллера
	 * Смотреть реализацию в модуле Helpers\Controller\ViewsController.php
     */
    function showAction() {
        
        // модель представления экшена Helloworld\Controller\Widget::indexAction() кэшируется в переменной $widget.
        $widget = $this->forward()->dispatch('Helloworld\Controller\Widget');

        // текущая кэшированная модель представления showAction.
        $page = new ViewModel(
            array(
                'greeting' => $this->greetingService->getGreeting(),
                'date' => $this->currentDate(),
            )
        );

        // addChild добавляет модель представления $widget в создаваемое свойство widgetContent объекта-модели представления $page
        $page->addChild($widget, 'widgetContent');
        
        // запустить модель представления $page со встроенной моделью представления $widget в свойстве widgetContent
        return $page;
        
        // $page.phtml:       
        // Теперь в виде экшена можно в разных местах вызывать переданные шаблоны c помощью хелпера renderChildModel()
            /*   
            <sidebar>
                   <?php echo $this->renderChildModel('widgetContent') ?>
            </sidebar>
            */
        /*
         * Таким образом можно подключать в виде модуля виды других модулей-виджетов.
         * Модули-виджеты, предназначенные для создания встраиваемых шаблонов, желательно размещать 
         * в отдельной директории widget на одном уровне с module и прописать в Zend/config/application.config.php:
         */ 
        'module_listener_options' => array(
            'module_paths' => array(
                './module',
                './vendor',
                './widget',
            )
        )
       
    }

}
