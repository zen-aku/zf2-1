<?php
/*
 * Встроенные хелперы моделей представлений ZF2 размещены в директории Zend\View\Helper\.
 * Хелперы наследуются от класса Zend\View\Helper\AbstractHelper, в объект которого 
 * автоматически встраивается объект модели представления, из которого идёт обращение к хелперу.
 */
namespace Zend\View\Helper;
use Zend\View\Renderer\RendererInterface as Renderer;

abstract class AbstractHelper implements HelperInterface {
    /**
     * Объект Zend\View\Renderer\RendererInterface модели представления, из которой идёт обращение к хелперу
     * Если обращение к хелперу производится из шаблонов вида, то это объект Zend\View\Renderer\PhpRenderer
     * @var Renderer
     */
    protected $view = null;

    /**
     * Set the View object
     * @param  Renderer $view
     * @return AbstractHelper
     */
    function setView(Renderer $view) {
        $this->view = $view;
        return $this;
    }

    /**
     * Get the view object
     * @return null|Renderer
     */
    function getView() {
        return $this->view;
    }
}
    /*
     * Если необходимо изнутри хелпера обратиться к другому хелперу, то достаточно вызвать метод getView() и у него вызвать хелпер
     */
     class SpecialPurpose extends AbstractHelper {
        protected $count = 0;
        function __invoke() {
            $this->count++;
            $output  = sprintf("I have seen 'The Jerk' %d time(s).", $this->count);
            // получаем текущий объект представления и вызываем у него хелпер
            $escaper = $this->getView()->plugin('escapehtml');
            return $escaper($output);
        }
    }
 
   /*
    * Все зарегистрированные в HelperPluginManager хелперы  вызываются
    * с помощью Zend/View/Renderer/PhpRenderer::__call($method, $argv), где $method - имя хелпера, $argv - передаваемые ему аргументы.
    * Хелпер может быть простым классом. И тогда методом __call() создаётся объект хелпера и возвращается объект, к методам которого и ведётся обращение.
    *   $this->escapecss()->escape($value);
    * Если хелперы представляют собой callback-классы, реализующие метод __invoke(), в котором и прописан функционал плагина, 
    * то обращение к хелперу осуществляется как к callback-методу. 
    * Метод __call() извлекает из HelperPluginManager требуемый плагин $method и обращается к нему как к callable ,
    * передавая ему массив параметров $params.
    *   $this->basepath();
    */ 
    function __call($method, $argv){
        if (!isset($this->__pluginCache[$method])) {
            $this->__pluginCache[$method] = $this->plugin($method);
        }
        if (is_callable($this->__pluginCache[$method])) {
            return call_user_func_array($this->__pluginCache[$method], $argv);
        }
        return $this->__pluginCache[$method];
    }
    
    
  /*
   * Собственные хелперы должны быть унаследованы от Zend\View\Helper\AbstractHelper и 
   * реализовывать метод __invoke(), если надо чтобы обращение к плагину было как к методу без вызова дополнительных методов плагина.
   * Собственные хелперы рекомендуется размещать в директории module/src/Modulemame/View/Helper/, 
   * если этот хелпер не планируется использовать в других модулях, и в module/src/Application/View/Helper/,
   * если хелпер планируется использовать в других модулях.
   * Собственные хелперы надо зарегистрировать в module.config.php либо в классе Module::getControllerPluginConfig().
   * Имя хелпера должно совпадать с методом, к которому будет обащение как к хелперу ($this->displayCurrentDate())
   * <имя плагина> => <путь к плагину>
   * После регистрации, этот помощник может быть вызван во всех представлениях всех модулей
   */
  /*
   * Порядок создания своего хелпера:
   * 1. Прописать в конфиге Module::getViewHelperConfig() альяс хелпера, используемый для вызова
   * 2. Прописать в autoload_classmap вызываемый класс
   * 3. Создать в директории module/view/helper/ класс хелпера и сделать его extends AbstractHelper
   * 4. Если надо получать какие-то действия при вызове хелпера, то прописать в нём __invoke()
   */
	
    namespace Helloworld\View\Helper;
    use Zend\View\Helper\AbstractHelper;

    class DisplayCurrentDate extends AbstractHelper {
        function __invoke() {
            return date('d.m.Y');
        }
    }

    // Module::getViewHelperConfig()
    function getViewHelperConfig() {
        return array(
            'invokables' => array(
                'displayCurrentDate' => 'Helpers\View\Helper\DisplayCurrentDate',
            )
        );
    }
    
    /*
     * Способы вызова хелперов:
     */  
    // Из плагин-менеджера
    $pluginManager = $view->getHelperPluginManager();
    $helper        = $pluginManager->get('displayCurrentDate');

    // Retrieve the helper instance, via the method "plugin", which proxies to the plugin manager:
    // Вернуть объект хелпера из метода "plugin", который обращается к плагин менеджеру
    $helper = $view->plugin('displayCurrentDate');

    // If the helper does not define __invoke(), the following also retrieves it:
    // Вернуть объект хелпера, если он не содержит __invoke()
    $helper = $view->displayCurrentDate();

    // If the helper DOES define __invoke, you can call the helper as if it is a method:
    // Вызвать метод __invoke() хелпера
    $filtered = $view->displayCurrentDate('some value');
         
  /*
   * Все собственные хелперы регистрируются в Zend\View\HelperPluginManager extends AbstractPluginManager extends ServiceManager, 
   * а встроенные хелперы дефолтно прописаны в свойствах HelperPluginManager:
   */  
    protected $factories = array(
        'flashmessenger' => 'Zend\View\Helper\Service\FlashMessengerFactory',
        'identity'       => 'Zend\View\Helper\Service\IdentityFactory',
    );

    protected $invokableClasses = array(
        // basepath, doctype, and url are set up as factories in the ViewHelperManagerFactory.
        // basepath and url are not very useful without their factories, however the doctype
        // helper works fine as an invokable. The factory for doctype simply checks for the
        // config value from the merged config.
        'basepath'            => 'Zend\View\Helper\BasePath',
        'cycle'               => 'Zend\View\Helper\Cycle',
        'declarevars'         => 'Zend\View\Helper\DeclareVars',
        'doctype'             => 'Zend\View\Helper\Doctype', // overridden by a factory in ViewHelperManagerFactory
        'escapehtml'          => 'Zend\View\Helper\EscapeHtml',
        'escapehtmlattr'      => 'Zend\View\Helper\EscapeHtmlAttr',
        'escapejs'            => 'Zend\View\Helper\EscapeJs',
        'escapecss'           => 'Zend\View\Helper\EscapeCss',
        'escapeurl'           => 'Zend\View\Helper\EscapeUrl',
        'gravatar'            => 'Zend\View\Helper\Gravatar',
        'headlink'            => 'Zend\View\Helper\HeadLink',
        'headmeta'            => 'Zend\View\Helper\HeadMeta',
        'headscript'          => 'Zend\View\Helper\HeadScript',
        'headstyle'           => 'Zend\View\Helper\HeadStyle',
        'headtitle'           => 'Zend\View\Helper\HeadTitle',
        'htmlflash'           => 'Zend\View\Helper\HtmlFlash',
        'htmllist'            => 'Zend\View\Helper\HtmlList',
        'htmlobject'          => 'Zend\View\Helper\HtmlObject',
        'htmlpage'            => 'Zend\View\Helper\HtmlPage',
        'htmlquicktime'       => 'Zend\View\Helper\HtmlQuicktime',
        'inlinescript'        => 'Zend\View\Helper\InlineScript',
        'json'                => 'Zend\View\Helper\Json',
        'layout'              => 'Zend\View\Helper\Layout',
        'paginationcontrol'   => 'Zend\View\Helper\PaginationControl',
        'partialloop'         => 'Zend\View\Helper\PartialLoop',
        'partial'             => 'Zend\View\Helper\Partial',
        'placeholder'         => 'Zend\View\Helper\Placeholder',
        'renderchildmodel'    => 'Zend\View\Helper\RenderChildModel',
        'rendertoplaceholder' => 'Zend\View\Helper\RenderToPlaceholder',
        'serverurl'           => 'Zend\View\Helper\ServerUrl',
        'url'                 => 'Zend\View\Helper\Url',
        'viewmodel'           => 'Zend\View\Helper\ViewModel',
    );
  
    /*
     * Все хелперы, как и плагины, по сути являются частными сервис-локаторами, унаследованными от главного сервис-локатора,
     * и их можно записывать и вызывать как сервисы, но использовать метод обращения не к ServiceManager, а к соответствующему менеджеру.
     * PluginManager и HelperPluginManager extends ServiceManager, поэтому им доступны методы ServiceManager
     * (напр. в примере ниже setService() и get())
     */
    // Объект какого-то класса
    $helper = new MyModule\View\Helper\LowerCase;

    // регистрируем как хелпер в HelperPluginManager
    $view->getHelperPluginManager()->setService('lowercase', $helper);   
    // регистрируем как плагин в PluginManager
    $controller->getPluginManager()->setService('lowercase', $helper);  
    // ругистрируем как сервис
    $objectAwareServiceManager->getServiceLocator()->setService('lowercase', $helper);
    
    // Аналогичным путём можно и вызывать хелперы и плагины как сервисы
    $view->getHelperPluginManager()->get('lowercase');
    $controller->getPluginManager()->get('lowercase');
    $objectAwareServiceManager->getServiceLocator()->get('lowercase');