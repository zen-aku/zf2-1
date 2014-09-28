<?php

class IndexController extends AbstractActionController {
    function indexAction() {	
		/*
		 * Zend\Mvc\Controller\Plugin\PostRedirectGet::__invoke($redirect = null, $redirectToUrl = false)
		 *
		 * 1. Если запрос Post, то post-данные сохраняются в контейнере сессии и возвращается объект Response - редирект на указанный адрес в $redirect
		 * используя статус 303 (GET-запрос). Если не указывать $redirect, произойдёт обновление сираницы со статусом 303.
		 */
					if ($redirectToUrl === false)
						$response = $redirector->toRoute($redirect); // $redirect - имя ключа массива роута в конфиге
					else 
						$response = $redirector->toUrl($redirect);   // $redirect - Url 
					$response->setStatusCode(303);
					return $response;
		/*		
		 * 2. Если запрос GET, то проверяется есть ли post-данные (с предыдущего post-запроса) в контейнере сессии 
		 * и возвращает:
		 *		- false, если контейнер сессии пуст
		 *		- массив (объект \Traversable) post-данных из контейнера сессии, если контейнер сессии не пуст
		 */
					
								
		$prg = $this->postRedirectGet();
		// вызов с помощью альяса
		//$prg = $this->prg();
		
		if ( $prg instanceof \Zend\Http\PhpEnvironment\Response )
            // если получили объект Response (после post-запроса) - вернуть его(редиректить по адресу $redirect)
            return $prg; 
		
		// если есть post-данные в контейнере сессии с предыдущего post-запроса
        elseif ($prg !== false) {
            // обрабатываем массив post-данных $prg         
        }      
			
	}
}


/**
* If a null value is present for the $redirect, the current route is
* retrieved and use to generate the URL for redirect.
*
* If the request method is POST, creates a session container set to expire
* after 1 hop containing the values of the POST. It then redirects to the
* specified URL using a status 303.
*
* If the request method is GET, checks to see if we have values in the
* session container, and, if so, returns them; otherwise, it returns a
* boolean false.
*
* @param  null|string $redirect
* @param  bool        $redirectToUrl
* @return \Zend\Http\Response|array|\Traversable|false
*/
function __invoke($redirect = null, $redirectToUrl = false) {
   $controller = $this->getController();
   $request    = $controller->getRequest();
   $container  = $this->getSessionContainer();

   if ($request->isPost()) {
	   $container->setExpirationHops(1, 'post');
	   $container->post = $request->getPost()->toArray();
	   return $this->redirect($redirect, $redirectToUrl);
   } else {
	   if ($container->post !== null) {
		   $post = $container->post;
		   unset($container->post);
		   return $post;
	   }
	   return false;
   }
}