<?php

class IndexController extends AbstractActionController {
    function indexAction() {
        /*
         * Zend\Mvc\Controller\Plugin\Identity::__invoke()
         * Плагин позволяет получить идентичность из сервиса Zend\Authentication\AuthenticationService
         * Плагин работает, если в ServiceManager зарегистрирован сервис Zend\Authentication\AuthenticationService
         * При вызове Identity::__invoke() идёт обращение к методу getIdentity() сервиса Zend\Authentication\AuthenticationService, 
         * который возвращает из сессии аутентифицированного мембера или null, если его нет
         */
        $user = $this->identity(); 
        
        // Использование identity() для проверки входа:        
        if ($user = $this->identity()) {
            // someone is logged !
        } else {
            // not logged in
        }
    }
    
}

/////////////////////////////////////////////////////////////////////////////////
class Identity extends AbstractPlugin {

    protected $authenticationService;

    function getAuthenticationService() {
        return $this->authenticationService;
    }
    
    function setAuthenticationService(AuthenticationService $authenticationService) {
        $this->authenticationService = $authenticationService;
    }

    /**
     * Retrieve the current identity, if any. If none is present, returns null.
     */
    function __invoke() {
        if (!$this->authenticationService instanceof AuthenticationService) {
            throw new Exception\RuntimeException('No AuthenticationService instance provided');
        }
        if (!$this->authenticationService->hasIdentity()) {
            return null;
        }
        return $this->authenticationService->getIdentity();
    }
}


    function Zend\Authentication\AuthenticationService::getIdentity(){
        $storage = $this->getStorage();
        if ($storage->isEmpty()) {
            return null;
        }
        return $storage->read();
    }
    
    function Zend\Authentication\Storage\Session::read() {
        return $this->session->{$this->member};
    }