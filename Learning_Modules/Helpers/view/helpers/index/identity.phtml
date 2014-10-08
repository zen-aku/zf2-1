<?php
/*
 * Хелпер identity() выполнен и работает аналогично плагину контроллера identity()
 * Хелпер позволяет получить идентичность из сервиса Zend\Authentication\AuthenticationService
 * Хелпер работает, если в конфиге ServiceManager зарегистрирован сервис Zend\Authentication\AuthenticationService
 * 'invokables' => array(
 *		'Zend\Authentication\AuthenticationService' => 'Zend\Authentication\AuthenticationService',
 *	),
 * Т.к. этот сервис требуется фактически для всех модулей, то его лучше регистрировать в общем модуле Application
 * 
 * Хелпер 'identity' вызывается через фабрику Zend\View\Helper\Service\IdentityFactory,
 * в которой вызывается сервис Zend\Authentication\AuthenticationService
 * При вызове Identity::__invoke() идёт обращение к методу getIdentity() сервиса Zend\Authentication\AuthenticationService, 
 * который возвращает из сессии имя аутентифицированного мембера или null, если его нет
 */
 
// если $this->identity() возвращает имя аутентифицированного мембера, то Logged, иначе Not logged
if ($user = $this->identity()) {
	echo 'Logged in as ' . $this->escapeHtml($user->getUsername());
} else {
	echo 'Not logged in';
}
