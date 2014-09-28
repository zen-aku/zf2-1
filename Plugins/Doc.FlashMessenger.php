<?php

class IndexController extends AbstractActionController {
    function indexAction() {
        
        /*
         * Сообщения для пользователя могут переноситься при переходе между страницами с
         * помощью Flash messenger. С технической точки зрения для этого на стороне сервера
         * генерируется сессия и в ней кратковременно сохраняется соответствующее сообщение.
         *    
         * FlashMessenger extends IteratorAggregate, Countable
         * и реализует итератор и счётчик из массива сообщений.
         */
                function getIterator() {
                    if ($this->hasMessages()) {
                        return new ArrayIterator($this->getMessages());
                    }
                    return new ArrayIterator();
                }
                function count() {
                    if ($this->hasMessages()) {
                        return count($this->getMessages());
                    }
                    return 0;
                }
        
        /*
         * Возвращает объект-итератор сообщений класса-плагина  Zend\Mvc\Controller\Plugin\FlashMessenger
         * Его можно проитерировать с помощью foreach, извлекая из него все сообщения для текущего неймспейса
         */
        $iterator = $this->flashMessenger();
        foreach ($iterator as $messages) echo $messages;
        
        /*
         * Константы FlashMessenger::NAMESPACE_...
         * Определяют к какой области относятся сообщения.
         * Это ключи конейнера сессии, где хранятся SplQueue сообщений:
         * $container->{NAMESPACE_...}->push($message);
         * Можно задавать свой ключ очереди сообщений с помощью setNamespace(($namespace = 'default')
         * По умолчанию все сообщения заносятся в очередь с ключом NAMESPACE_DEFAULT
         */
        const NAMESPACE_DEFAULT = 'default';
        const NAMESPACE_SUCCESS = 'success';
        const NAMESPACE_WARNING = 'warning';
        const NAMESPACE_ERROR = 'error';
        const NAMESPACE_INFO = 'info';
		
		/*
		 * getMessages() - возвращает массив FlashMessenger::messages сообщений предыдущего запроса 
		 * (после редиректа с сообщением в контейнере сессии) и удаляет контейнер сессии предыдущего запроса.
		 * Если сообщений с предыдущего запроса нет, то getMessages() возвращает массив сообщений текущего запроса(как getCurrentMessages())
		 * 
		 * getCurrentMessages() - возвращает текущие сообщения из контейнера сессии и тут же удаляет его.
		 * Т.е. после редиректа getCurrentMessages() ничего не вернёт. Поэтому getCurrentMessages() должен
		 * вызываться в том же экшене, где было добавлено сообщение через addMessages(), после него. 
         * Все Current-методы применяются для тестирования и на практике малоприменимы.
		 */				

/// Общие методы //////////////////////////////////////////////////               
        /*
         * FlashMessenger::count()
         * Счётчик сообщений в контейнере сессии для текущего неймспейса
         */
        $count = $this->flashMessenger()->count();
 
        /*
         * FlashMessenger::setNamespace($namespace = 'default')
         * Задать неймспейс, для которого будет добавляться/вызываться/проверяться сообщение
         */
        $this->flashMessenger()->setNamespace('Users');  
 
        /*
         * FlashMessenger::getNamespace()
         * Получить текущий неймспейс
         */
        $namespace = $this->flashMessenger()->getNamespace();  
         
/// Блок Messages - сообщения /////////////////////////////////////               
        /*
         * FlashMessenger::hasMessages()
         * Проверяет есть ли сообщения(массив) в контейнере сессии для текущего неймспейса
         */
        $hasMessages = $this->flashMessenger()->hasMessages(); 
        
        /*
         * FlashMessenger::hasCurrentMessages()
         * Есть ли текущее сообщение в контейнере сессии для текущего неймспейса
         */
        $hasCurrentMessages = $this->flashMessenger()->hasCurrentMessages();       
        
        /*
         * FlashMessenger::addMessage($message)
         * Добавляет сообщение в контейнер сессии (SplQueue) для текущего неймспейса
         *      $container->{$namespace} = new SplQueue();
         *      $container->{$namespace}->push($message);
         */
        $this->flashMessenger()
            ->addMessage('Сообщение1')
            ->addMessage('Сообщение2');       
        
        /*
         * FlashMessenger::getMessages()
         * Возвращает массив сообщений для текущего неймспейса
         */
        $messages = $this->flashMessenger()->getMessages();
        
        /*
         * FlashMessenger::getCurrentMessages()
         * Возвращает массив текущих сообщений(были добавлены в этом запросе) для текущего неймспейса
         */
        $currentMessages = $this->flashMessenger()->getCurrentMessages();
         
/// Блок "info" namespace - инфосообщения (для всего приложения) ///////////////                                  
        /*
         * FlashMessenger::hasInfoMessages()
         * Есть ли сообщения(массив) в контейнере сессии для неймспейса NAMESPACE_INFO
         */
        $hasInfoMessages = $this->flashMessenger()->hasInfoMessages(); 
        
        /*
         * FlashMessenger::hasCurrentInfoMessages()
         * Есть ли текущее сообщение в контейнере сессии для неймспейса NAMESPACE_INFO
         */
        $hasCurrentInfoMessages = $this->flashMessenger()->hasCurrentInfoMessages();  
        
        /*
         * FlashMessenger::addInfoMessage($message)
         * Добавляет сообщения в контейнер сессии (SplQueue) для неймспейса NAMESPACE_INFO
         *      $container->{'info'}->push($message);
         */
        $this->flashMessenger()
            ->addInfoMessage('Инфа1')
            ->addInfoMessage('Инфа2');       
        
        /*
         * FlashMessenger::getInfoMessages()
         * Возвращает массив сообщений для неймспейса NAMESPACE_INFO
         */
        $infoMessages = $this->flashMessenger()->getInfoMessages();
        
        /*
         * FlashMessenger::getCurrentInfoMessages()
         * Возвращает массив текущих сообщений(были добавлены в этом запросе) для неймспейса NAMESPACE_INFO
         */
        $currentInfoMessages = $this->flashMessenger()->getCurrentInfoMessages();
            
/// Блок "success" namespace - сообщения об успехе (для всего приложения) ///////////////                 
        /*
         * FlashMessenger::hasSuccessMessages()
         * Есть ли сообщения(массив) в контейнере сессии для неймспейса "success"
         */
        $hasSuccessMessages = $this->flashMessenger()->hasSuccessMessages(); 
        
        /*
         * FlashMessenger::hasCurrentSuccessMessages()
         * Есть ли текущее сообщение в контейнере сессии для неймспейса "success"
         */
        $hasCurrentSuccessMessages = $this->flashMessenger()->hasCurrentSuccessMessages();  
        
        /*
         * FlashMessenger::addSuccessMessage($message)
         * Добавляет сообщения в контейнер сессии (SplQueue) для неймспейса "success"
         *      $container->{'success'}->push($message);
         */
        $this->flashMessenger()->addSuccessMessage('Вы успешно вошли!');               
        
        /*
         * FlashMessenger::getSuccessMessages()
         * Возвращает массив сообщений для неймспейса 'success'
         */
        $successMessages = $this->flashMessenger()->getSuccessMessages(); 
        
        /*
         * FlashMessenger::getCurrentSuccessMessages()
         * Возвращает массив текущих сообщений(были добавлены в этом запросе) для неймспейса 'success'
         */
        $currentSuccessMessages = $this->flashMessenger()->getCurrentSuccessMessages();
            
/// Блок "warning" namespace - сообщения о предупреждении (для всего приложения) ///////////////               
        /*
         * FlashMessenger::hasWarningMessages()
         * Есть ли сообщения(массив) в контейнере сессии для неймспейса "warning"
         */
        $hasWarningMessages = $this->flashMessenger()->hasWarningMessages(); 
        
        /*
         * FlashMessenger::hasCurrentWarningMessages()
         * Есть ли текущее сообщение в контейнере сессии для неймспейса "warning"
         */
        $hasCurrentWarningMessages = $this->flashMessenger()->hasCurrentWarningMessages();  
        
        /*
         * FlashMessenger::addWarningMessage($message)
         * Добавляет сообщения в контейнер сессии (SplQueue) для неймспейса "warning"
         *      $container->{'warning'}->push($message);
         */
        $this->flashMessenger()->addWarningMessage('Предупреждение!');           
        
        /*
         * FlashMessenger::getWarningMessages()
         * Возвращает массив сообщений для неймспейса 'warning'
         */
        $warningMessages = $this->flashMessenger()->getWarningMessages(); 
        
        /*
         * FlashMessenger::getCurrentWarningMessages()
         * Возвращает массив текущих сообщений(были добавлены в этом запросе) для текущего неймспейса 'warning'
         */
        $currentWarningMessages = $this->flashMessenger()->getCurrentWarningMessages();
        
/// Блок "error" namespace - сообщения об ошибке (для всего приложения) ///////////////               
        /*
         * FlashMessenger::hasErrorMessages()
         * Есть ли сообщения(массив) в контейнере сессии для неймспейса "error"
         */
        $hasErrorMessages = $this->flashMessenger()->hasErrorMessages();
         
        /*
         * FlashMessenger::hasCurrentErrorMessages()
         * Есть ли текущее сообщение в контейнере сессии для неймспейса "error"
         */
        $hasCurrentErrorMessages = $this->flashMessenger()->hasCurrentErrorMessages();  
        
        /*
         * FlashMessenger::addErrorMessage($message)
         * Добавляет сообщения в контейнер сессии (SplQueue) для неймспейса "error"
         *      $container->{'error'}->push($message);
         */
        $this->flashMessenger()->addErrorMessage('Ошибка!');         
        
        /*
         * FlashMessenger::getErrorMessages()
         * Возвращает массив сообщений для неймспейса 'error'
         */
        $errorMessages = $this->flashMessenger()->getErrorMessages(); 
        
        /*
         * FlashMessenger::getCurrentErrorMessages()
         * Возвращает массив текущих сообщений(были добавлены в этом запросе) для неймспейса 'error'
         */
        $currentErrorMessages = $this->flashMessenger()->getCurrentErrorMessages();
        
/// Блок удаления сообщений из контейнера сессий ////////////////////////                      
        /*
         * FlashMessenger::clearMessages()
         * Удалить все сообщения для текущего неймспейса
         */        
        $this->flashMessenger()->clearMessages();       
        
        /*
         * FlashMessenger::clearMessagesFromNamespace($namespaceToClear)
         * Удалить все сообщения для неймспейса $namespaceToClear
         */     
        $this->flashMessenger()->clearMessagesFromNamespace('Users');        
       
        /*
         * FlashMessenger::clearMessagesFromContainer()
         * Удалить все сообщения для всех неймспейсов
         */             
        $this->flashMessenger()->clearMessagesFromContainer();
        
        /*
         * FlashMessenger::clearCurrentMessages()
         * Удалить текущее сообщение (hasCurrentMessages()) для текущего неймспейса 
         * Используется после соответствующего getCurrentMessages()
         */            
        $this->flashMessenger()->clearCurrentMessages();
        
        /*
         * FlashMessenger::clearCurrentMessagesFromNamespace($namespaceToClear)
         * Удалить текущее сообщение для неймспейса $namespaceToClear
         */             
        $this->flashMessenger()->clearCurrentMessagesFromNamespace($namespaceToClear);
        
        /*
         * FlashMessenger::clearCurrentMessagesFromContainer()
         * Удалить текущие сообщения из контейнера сессий 
         */
        $this->flashMessenger()->clearCurrentMessagesFromContainer();
         
      
        
    }

}    