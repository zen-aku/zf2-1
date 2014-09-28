<?php
namespace Plugins\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Пример использования плагинов контроллеров.
 */
class MessengerController extends AbstractActionController {

	/**
	 * route: plugins/messenger
	 */
    function indexAction() {
		
		$this->flashMessenger()
			->addMessage('CurrentMessage')
			->addInfoMessage('CurrentInfoMessage')
			->addSuccessMessage('CurrentSuccessMessage')
			->addWarningMessage('CurrentWarningMessage')
			->addErrorMessage('CurrentErrorMessage');
		
        /*
         * Принудитедьное удаление сообщений
         */
        // Удалить все сообщения для текущего неймспейса ('warning')
        $this->flashMessenger()->setNamespace('warning')->clearMessages();                   
        
        // Удалить все сообщения для неймспейса 'error'   
        $this->flashMessenger()->clearMessagesFromNamespace('error');        
       
        //Удалить все сообщения для всех неймспейсов            
        //$this->flashMessenger()->clearMessagesFromContainer();
                     
		/*
		 * getMessages() - возвращает массив FlashMessenger::messages сообщений предыдущего запроса 
		 * (после редиректа из addMessageAction()).
		 */
        $this->flashMessenger()->setNamespace('default');
		$messages['messages'] = $this->flashMessenger()->getMessages();
		$messages['info'] = $this->flashMessenger()->getInfoMessages();
		$messages['success'] = $this->flashMessenger()->getSuccessMessages();
		$messages['warning'] = $this->flashMessenger()->getWarningMessages();
		$messages['error'] = $this->flashMessenger()->getErrorMessages();
					
        $current = array();
        /* getCurrentMessages() - возвращает текущие сообщения из контейнера сессии (все add() выше).
		 * Т.е. после редиректа getCurrentMessages() ничего не вернёт. Поэтому getCurrentMessages() должен
		 * вызываться в том же экшене, где было добавлено сообщение через addMessages(), после него. Используется для тестирования. 
		 */      
		$current['currentMessages'] = $this->flashMessenger()->getCurrentMessages();
		$current['currentInfo'] = $this->flashMessenger()->getCurrentInfoMessages();
		$current['currentSuccess']= $this->flashMessenger()->getCurrentSuccessMessages();
		$current['currentWarning'] = $this->flashMessenger()->getCurrentWarningMessages();
		$current['currentError'] = $this->flashMessenger()->getCurrentErrorMessages();
		
		
		// Устанавливаем неймспейс (ключ в массиве сообщений) 'users' и добавляем для него сообщения
		$this->flashMessenger()->setNamespace('users')->addMessage('currentUsers');
		$messages['users'] = $this->flashMessenger()->getMessages();
		
		return new ViewModel(
			array(
				'messages' => $messages,	
				'currentMessages' => $current,						
			)
    	);
	}
	
	/**
	 * route: plugins/messenger/addmessage
	 */
	function addMessageAction() {
		
		$this->flashMessenger()
            ->addMessage('Привет, мир!')
            ->addMessage('Доброе утро!')
			->addInfoMessage('Инфа')	
			->addSuccessMessage('Вы успешно вошли!')
			->addWarningMessage('Предупреждение!')
			->addErrorMessage('Ошибка!');
		
		// Устанавливаем неймспейс (ключ в массиве сообщений) 'users' и добавляем для него сообщения
		$this->flashMessenger()->setNamespace('users')->addMessage('Ваш логин Alex');
		
        $this->redirect()->toUrl('/plugins/messenger');
	}
        
}