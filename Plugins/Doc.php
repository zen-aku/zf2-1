<?php
/*
 * Встроенные плагины контроллеров ZF2 размещены в директории Zend\Mvc\Controller\Plugin\.
 * Плагины контроллеров наследуются от класса Zend\Mvc\Controller\Plugin\AbstractPlugin, в объект которого 
 * автоматически встраивается объект контроллера, из которого идёт обращение к плагину.
 */   
namespace Zend\Mvc\Controller\Plugin;
use Zend\Stdlib\DispatchableInterface as Dispatchable;

abstract class AbstractPlugin implements PluginInterface {
  
    protected $controller;      // объект Zend\Stdlib\DispatchableInterface текущего контроллера

    /**
     * Установить текущий контроллер
     */
    function setController(Dispatchable $controller) {
        $this->controller = $controller;
    }

    /**
     * Вернуть текущий контроллер
     */
    function getController() {
        return $this->controller;
    }
}
  
/*
 * Контроллеры унаследованы от AbstractController, в который автоматически встраивается PluginManager
 * c набором доступных плагинов (унаследованных от AbstractPlugin и зарегистрированных с помощью getControllerPluginConfig() - для пользовательских плагинов).
 * Обращение к плагину как к методу контроллера осуществляется с помошью магического метода AbstractController::__call($name_plugin, $params)
 * 
 * Плагин может быть простым классом(или объектом) с набором методов как класс Redirect. 
 * И тогда методом __call($name_plugin, $params) создаётся объект плагина и возвращается объект.
 *      $this->redirect()->toRoute('show') 
 * redirect() - __call('redirect') вернёт объект класса Redirect у которого вызывается метод toRoute()
 * 
 * Если плагины контроллеров представляют собой callback-классы, реализующие метод __invoke(),
 * в котором и прописан функционал плагина, то обращение к плагину осуществляется как к callback-методу. 
 * Метод __call() извлекает из PluginManager требуемый плагин $method и обращается к нему как к callable ,
 * передавая ему массив параметров $params.
 * 
 * В PluginManager жёстко прописаны имена встроенных в zf2 плагинов как invokable-сервисы, а сам PluginManager
 * наследует ServiceManager и потому плагины могут быть представлены и заданы аналогично сервисам(?)
 */
function __call( $method, $params ) {
    
    $plugin = $this->plugin($method); //$plugin =  $this->getPluginManager()->get($method);
    if (is_callable($plugin)) {
        return call_user_func_array($plugin, $params);
    }
    return $plugin;
}

/*
 * Пример вызова плагина Zend\Mvc\Controller\Plugin\PostRedirectGet из какого-нибудь экшена контроллера:
 * Обращение к плагину callback-классу PostRedirectGet как к методу postRedirectGet($param) (PostRedirectGet::__invoke($param))
 */
$this->postRedirectGet('/show', true);

/*
 * Пользовательские плагины контроллеров должны быть унаследованы от Zend\Mvc\Controller\Plugin\AbstractPlugin и 
 * реализовывать метод __invoke(), если надо чтобы обращение к плагину было как к методу без вызова дополнительных методов плагина.
 * Т.к. плагины контроллеров могут использоваться любыми контроллерами любых модулей, то их надо
 * размещать в модуле приложения в папке Application\src\Controller\Plugin и регистрировать их в конфиге Application или 
 * в Application\Module::getControllerPluginConfig().
 * И только специфичные контроллеры плагинов, используемые только в конкретном модуле, надо размещать в этом модуле
 * в ModuleName\src\Controller\Plugin и регистрировать их конфиге или в ModuleName\Module::getControllerPluginConfig().
 */
//namespace Helloworld\Controller\Plugin;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class CurrentDate extends AbstractPlugin {
    
    function __invoke() {
        return date('d.m.Y');
    }
}
// Обращение из контроллера:
$this->currentDate();

/*
 * Его надо зарегистрироровать в Module::getControllerPluginConfig()
 * или в общем конфиге модуля с ключом 'plugin_manager'
 */
function getControllerPluginConfig() {
    return array(
        'invokables' => array(
            'currentDate' => 'Helloworld\Controller\Plugin\CurrentDate',
        )
    );
}


//////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Plugin manager implementation for controllers
 * Registers a number of default plugins, and contains an initializer for
 * injecting plugins with the current controller.
 */
class PluginManager extends AbstractPluginManager {
    /**
     * Default set of plugins factories
     */
    protected $factories = array(
        'forward'  => 'Zend\Mvc\Controller\Plugin\Service\ForwardFactory',
        'identity' => 'Zend\Mvc\Controller\Plugin\Service\IdentityFactory',
    );
    /**
     * Default set of plugins
     */
    protected $invokableClasses = array(
        'acceptableviewmodelselector' => 'Zend\Mvc\Controller\Plugin\AcceptableViewModelSelector',
        'filepostredirectget'         => 'Zend\Mvc\Controller\Plugin\FilePostRedirectGet',
        'flashmessenger'              => 'Zend\Mvc\Controller\Plugin\FlashMessenger',
        'layout'                      => 'Zend\Mvc\Controller\Plugin\Layout',
        'params'                      => 'Zend\Mvc\Controller\Plugin\Params',
        'postredirectget'             => 'Zend\Mvc\Controller\Plugin\PostRedirectGet',
        'redirect'                    => 'Zend\Mvc\Controller\Plugin\Redirect',
        'url'                         => 'Zend\Mvc\Controller\Plugin\Url',
    );
    /**
     * Default set of plugin aliases
     */
    protected $aliases = array(
        'prg'     => 'postredirectget',
        'fileprg' => 'filepostredirectget',
    );
    /**
     * @var DispatchableInterface
     */
    protected $controller;

    /**
     * Retrieve a registered instance
     *
     * After the plugin is retrieved from the service locator, inject the
     * controller in the plugin every time it is requested. This is required
     * because a controller can use a plugin and another controller can be
     * dispatched afterwards. If this second controller uses the same plugin
     * as the first controller, the reference to the controller inside the
     * plugin is lost.
     *
     * @param  string $name
     * @param  mixed  $options
     * @param  bool   $usePeeringServiceManagers
     * @return mixed
     */
    function get($name, $options = array(), $usePeeringServiceManagers = true) {
        $plugin = parent::get($name, $options, $usePeeringServiceManagers);
        $this->injectController($plugin);
        return $plugin;
    }

    /**
     * Set controller
     * @param  DispatchableInterface $controller
     * @return PluginManager
     */
    function setController(DispatchableInterface $controller) {
        $this->controller = $controller;
        return $this;
    }

    /**
     * Retrieve controller instance
     * @return null|DispatchableInterface
     */
    public function getController() {
        return $this->controller;
    }

    /**
     * Inject a helper instance with the registered controller
     * @param  object $plugin
     * @return void
     */
    function injectController($plugin){
        if (!is_object($plugin)) {
            return;
        }
        if (!method_exists($plugin, 'setController')) {
            return;
        }
        $controller = $this->getController();
        if (!$controller instanceof DispatchableInterface) {
            return;
        }
        $plugin->setController($controller);
    }

    /**
     * Validate the plugin
     * Any plugin is considered valid in this context.
     * @param  mixed                            $plugin
     * @throws Exception\InvalidPluginException
     */
    function validatePlugin($plugin) {
        if ($plugin instanceof Plugin\PluginInterface) {
            // we're okay
            return;
        }
        throw new Exception\InvalidPluginException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Plugin\PluginInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
