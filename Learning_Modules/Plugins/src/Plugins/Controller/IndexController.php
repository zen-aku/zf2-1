<?php
namespace Plugins\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Пример использования плагинов контроллеров.
 */
class IndexController extends AbstractActionController {

	/**
	 * route: /plugins/index
	 */
    function indexAction() {

        if ( rand(1, 10) < 4 )
           /*
            * Zend\Mvc\Controller\Plugin\Redirect::refresh()
            * Обновить текущий route (редирект на текущий роут)
            */  
           $this->redirect()->refresh(); 
        
    	return new ViewModel(
    			array()
    	);
    }
    
    /**  
     * route: /plugins/index/toroute
     */
    function toRouteAction() {
        /*
         * Zend\Mvc\Controller\Plugin\Redirect::toRoute()
         * Редирект на указанный route (определяется в конфигах модулей)
         * Указывается имя ключа массива роута в конфиге.
         */
        $this->redirect()->toRoute('plugins');
    }
    
    /**
     * route: /plugins/index/tourl
     */
    function toUrlAction() {
        /*
         * Zend\Mvc\Controller\Plugin\Redirect::toUrl()
         * Редирект на указанный URL
         */
        $this->redirect()->toUrl('plugins/index');
    }
 
    /**
     * route: /plugins/index/prg
     */
    function prgAction() {
        /*
         * Zend\Mvc\Controller\Plugin\PostRedirectGet::__invoke($redirect = null, $redirectToUrl = false)
         * Сохраняет данные из POST-запроса в сессии и редиректит по указанному маршруту или на текущий маршрут, если другой не указан,
         * используя статус 303 (GET-запрос)
         * Если приходит после GET-запроса и есть данные в сессии с предыдущего post-запроса, то возвращается массив post-данных
         * Если нет данных, то возвращает false       
         */       
        $prg = $this->postRedirectGet('/plugins/index/prg', true);
		// вызов с помощью альяса
		//$prg = $this->prg();		
		if ( $prg instanceof \Zend\Http\PhpEnvironment\Response )
            // если получили объект Response (после post-запроса) - вернуть его(редиректить по адресу $redirect)
            return $prg; 		
		// если есть post-данные в контейнере сессии с предыдущего post-запроса
        elseif ($prg !== false) {
            // обрабатываем массив post-данных $prg         
        }            
        $this->redirect()->toRoute('plugins');
    }
	
	/**
	 * route: /plugins/index/url
	 */
	function urlAction() {		
		/*
		 * Url::fromRoute($route = null, $params = array(), $options = array(), $reuseMatchedParams = false)
		 * Генерирует Url из $route, $params и $options
		 * $route - имя ключа массива роута в конфиге
		 */
		$url = $this->url()->fromRoute('plugins');		
		//echo $url;
		//exit;		
		$this->redirect()->toUrl($url);		
	}
	
	/**
	 * route: /plugins/index/newlayout
	 */
	function newlayoutAction() {
		/*
		 * Layout::_invoke($template = null)
		 * Позволяет изменить шаблон вида для данного экшена.
		 * Новый шаблон должен быть прописан в конфиге в 'template_map'
		 * Если новый шаблон не задан, то вызывается шаблон по стандартному пути для данного экшена
		 */
		$this->layout('layout/EventsLayout');
		/*
		 * Надо обязательно создавать главный вид по экшену: view/plugins/index/newlayout.phtml
		 * или указывать в 'template_map' другой путь к главному виду:
		 * <модуль>/<контроллер>/<экшен>  => __DIR__ . '/../<путь к шаблон.phtml>
         * 'plugins/index/newlayot' => __DIR__ . '/../view/plugins/index/other.phtml',  
		 */
	}
    
    /**
     * route: /plugins/index/params
     */
    function paramsAction() {
        /*
         * Params::__invoke()::fromHeader($param = null, $default = null)
         * Получить параметры из заголовка запроса
         * $param - имя возвращаемого параметра в массиве HEADER(?).
         * Если $param == null, возвращается ассоц массив всех параметров из HEADER
         */
        $header = $this->params()->fromHeader();       
        /*
         * Params::__invoke()::fromRoute($param = null, $default = null)
         * Получить параметр из Route (из роута, по которому было обращение к данному контроллеру)
         * $param - имя параметра роута (параметры роута в конфиге?)
         * Если $param == null, возвращается ассоц массив всех параметров роута, по которому был осуществлён запрос
         */
        $route = $this->params()->fromRoute();
        /*
         * Params::__invoke()::fromFiles($name = null, $default = null)
         * Получить файл по имени из запроса
         * $param - имя возвращаемого файла в массиве передаваемых файлов в запросе.
         * Если $param == null, возвращается ассоц массив всех передаваемых файлов в запросе
         */
        $file = $this->params()->fromFiles();
        /*
         * Params::__invoke()::fromQuerry($param = null, $default = null)
         * Получить параметры из GET.
         * $param - имя возвращаемого параметра в массиве GET.
         * Если $param == null, возвращается ассоц массив всех параметров из GET
         */
        $query = $this->params()->fromQuery();
        /*
         * Params::__invoke()::fromPost($param = null, $default = null)
         * Получить параметры из POST.
         * $param - имя возвращаемого параметра в массиве POST. 
         * Если $param == null, возвращается ассоц массив всех параметров из POST
         */
        $post = $this->params()->fromPost();
       
        return new ViewModel(
            array(
                'header' => $header,
                'route' => $route,
                'file' => $file,
                'query' => $query,
                'post' => $post,
            )
    	);
    } 
        
    /**
     * route: /plugins/index/forward
     */
    function forwardAction() {
        /*
         * Вызываем контроллер 'Plugins\Controller\Messenger' и его экшен addMessageAction()
         */
        return $this->forward()->dispatch(
            'Plugins\Controller\Messenger', 
            array(
                'action' => 'addMessage'
            )
        );
    }
    
    /**
     * route: /plugins/index/identity
     */
    function identityAction() {
        
        if ($this->identity()) {
            // someone is logged !
            return new ViewModel(
    			array()
            );           
        } else 
            // not logged in
            $this->redirect()->toUrl('/plugins/index');     
    }
    
    /**
     * route: /plugins/index/currentdate
     * Вызов пользовательского плагина currentDate() (Plugins\Plugin\CurrentDate)
     */
    function currentDateAction() {
       
        return new ViewModel(
            array(
                'date' => $this->currentDate(),
            )
        );
    }
    
}