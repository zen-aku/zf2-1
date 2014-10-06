<?php
/*
 * Хелпер declareVars() позволяет из вида декларировать новые переменные-свойства вида 
 * (создавать новые публичные свойство объекта вида: Zend\View\Renderer\RendererInterface - PhpRenderer)
 * Если новое свойство задекларировано без значения, то ему автоматически присвоится пустая строка "" 
 */
$this->declareVars(
   'varName1',
   'varName2',
    array('varName3' => 'defaultValue',
          'varName4' => array()
    )
);

// Обращение к задекларированным свойствам как к свойствам объекта View
echo $this->varName1;
echo $this->varName3;

////////////////////////////////////////////////////////////////////////////////////////////////////
/*
 * Механизм декларирования свойств:
 * Хелпер вызывает декларируемое свойство объекта Zend\View\Renderer\RendererInterface(PhpRenderer) и передаёт ему значение.
 * Объект RendererInterface с помощью __set($name) и __get($name) перехватывает обращение к декларируемому свойству и 
 * перенаправляет на соответствующий ключ массива PhpRenderer::__vars[$name].
 * 
 * 
 * В объекте вида класса PhpRenderer сохраняются декларируемые переменные в массиве-хранилище:
 * PhpRenderer::__vars['имя деклприруемой переменной'] => <значение деклприруемой переменной>
 * PhpRenderer::__vars - это объект Zend\View\Variables extends ArrayObject.
 * 
 * По-видимому таким путём заносятся в объект View данные из экшеновконтроллеров.
 */

class DeclareVars extends AbstractHelper {
    
    function __invoke() {
        $view = $this->getView();
        $args = func_get_args();
        foreach ($args as $key) {
            if (is_array($key)) {
                foreach ($key as $name => $value) {
                    $this->declareVar($name, $value);
                }
            } elseif (!isset($view->vars()->$key)) {
                $this->declareVar($key);
            }
        }
    }
    /**
     * Set a view variable. Checks to see if a $key is set in the view object; if not, sets it to $value.
     * @param  string $key
     * @param  string $value Defaults to an empty string
     * @return void
     */
    protected function declareVar($key, $value = '') {
        // получить объект вида (Zend\View\Renderer\RendererInterface)
        $view = $this->getView();
        // получить у вида объект-массив Zend\View\Variables, в котором хранятся декларируемые переменные
        $vars = $view->vars();
        if (!isset($vars->$key)) {
            // добавить в объект-массив Zend\View\Variables новую переменную
            $vars->$key = $value;
        }
    }
}

class PhpRenderer implements Renderer, TreeRendererInterface {   
    /**
     * Массив переменных , декларируемых (объявляемых/создаваеиых) из вида с помощью хелпера declareVars()
     * @var Zend\View\Variables extends ArrayObject
     */
    private $__vars;
    
     /**
     * Set variable storage. Expects either an array, or an object implementing ArrayAccess.
     * @param  array|ArrayAccess $variables
     * @return PhpRenderer
     * @throws Exception\InvalidArgumentException
     */
    function setVars($variables) {
        if (!is_array($variables) && !$variables instanceof ArrayAccess) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected array or ArrayAccess object; received "%s"',
                (is_object($variables) ? get_class($variables) : gettype($variables))
            ));
        }
        // Enforce a Variables container
        if (!$variables instanceof Variables) {
            $variablesAsArray = array();
            foreach ($variables as $key => $value) {
                $variablesAsArray[$key] = $value;
            }
            $variables = new Variables($variablesAsArray);
        }
        $this->__vars = $variables;
        return $this;
    }

    /**
     * Get a single variable, or all variables
     * @param  mixed $key
     * @return mixed
     */
    function vars($key = null) {
        if (null === $this->__vars) {
            $this->setVars(new Variables());
        }
        if (null === $key) {
            return $this->__vars;
        }
        return $this->__vars[$key];
    }

    /**
     * Get a single variable
     * @param  mixed $key
     * @return mixed
     */
    function get($key) {
        if (null === $this->__vars) {
            $this->setVars(new Variables());
        }
        return $this->__vars[$key];
    }

    /**
     * Overloading: proxy to Variables container
     * @param  string $name
     * @return mixed
     */
    function __get($name){
        $vars = $this->vars();
        return $vars[$name];
    }

    /**
     * Overloading: proxy to Variables container
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    function __set($name, $value) {
        $vars = $this->vars();
        $vars[$name] = $value;
    }

    /**
     * Overloading: proxy to Variables container
     * @param  string $name
     * @return bool
     */
    function __isset($name){
        $vars = $this->vars();
        return isset($vars[$name]);
    }

    /**
     * Overloading: proxy to Variables container
     * @param  string $name
     * @return void
     */
    function __unset($name){
        $vars = $this->vars();
        if (!isset($vars[$name])) {
            return;
        }
        unset($vars[$name]);
    }
}


