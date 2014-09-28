<?php
namespace Zend\Di;

/*
 * Менеджер объектов - хранилище объектов (реестр экземпляров объектов), предназначенное для хранения и получения по требованию
 * объектов из хранилища с определённым набором параметров
 */
class InstanceManager {
    
    /**
     * Mассив общедоступных объектов в виде:
     *  [string $classOrAlias] = object $instance
     * @var array
     */
    protected $sharedInstances = array();
    
    /**
     * Mассив объектов с параметрами в виде: 
     *  ['hashShort'][string $hashKey] = true;
     *  ['hashLong' ][string $hashKey.'/'.$hashValue] = object $instance
     *      $hashKey = createHashForKeys($classOrAlias, $paramKeys) - string: имя класса/альяса . параметры
     *      $hashValue = createHashForValues($classOrAlias, $paramValues) - string: hash параметров объекта
     * @var array
     */
    protected $sharedInstancesWithParams = array('hashShort' => [], 'hashLong' => []);
    
    /**
     * Массив альяс => класс
     *  aliases[string $alias] = string $class;
     * @var array key: alias, value: class
     */
    protected $aliases = array();         
    
    /**
     * Массив конфигурационных данных $aliasOrClass
     *  $this->configurations[$aliasOrClass] = $this->configurationTemplate
     * @var array
     */
    protected $configurations = array();
    
    /**
     * Шаблон массива для конфигурационной информации в элементе configurations[$aliasOrClass]
     * @var array
     */
    protected $configurationTemplate = array(
        /**
         * alias|class => alias|class
         * interface|abstract => alias|class|object
         * name => value
         */
        'parameters' => array(),
        /**
         * injection type => array of ordered method params
         */
        'injections' => array(),
        /**
         * alias|class => bool
         */
        'shared' => true
    );
    
    /**
     * An array of globally preferred implementations for interfaces/abstracts
	 * Массив глобально предпочтительных реализаций(типов) для интерфейсов / абстракций
	 *  typePreferences[$interfaceOrAbstract][] = $preferredImplementation
     * @var array
     */
    protected $typePreferences = array();     
    
    ///////////////////////////////// sharedInstance ///////////////////////////////////////////////
    /**
     * Есть ли в массиве объектов sharedInstances объект класса $classOrAlias
     * @param  string $classOrAlias
     * @return bool
     */
    function hasSharedInstance( $classOrAlias ) {
        return isset($this->sharedInstances[$classOrAlias]);
    }

    /**
     * Вернуть объект класса $classOrAlias из массива объектов sharedInstances
     * @param  string $classOrAlias
     * @return object
     */    
    function getSharedInstance( $classOrAlias ) {
        return $this->sharedInstances[$classOrAlias];
    }

    /**
     * Добавить объект $instance класса $classOrAlias в массив объектов sharedInstances
     * @param object $instance
     * @param string $classOrAlias
     * @throws Exception\InvalidArgumentException
     */
    function addSharedInstance( $instance, $classOrAlias ) {
        if ( !is_object($instance) ) {
            throw new Exception\InvalidArgumentException('This method requires an object to be shared. Class or Alias given: ' . $classOrAlias);
        }
        $this->sharedInstances[$classOrAlias] = $instance;
    }
    ////////////////////////////////// sharedInstanceWithParameters ////////////////////////////////
    /**
	 * Создать ключ для массива объектов с параметрами
     *  ['hashShort'][string $hashKey] = true;
     *  ['hashLong' ][string $hashKey.'/'.$hashValue] = object $instance
     * @param  string   $classOrAlias
     * @param  string[] $paramKeys
     * @return string
     */
    protected function createHashForKeys( $classOrAlias, $paramKeys ) {
        return $classOrAlias . ':' . implode('|', $paramKeys);
    }

    /**
	 * Создать ключ для массива объектов с параметрами
     *  ['hashShort'][string $hashKey] = true;
     *  ['hashLong' ][string $hashKey.'/'.$hashValue] = object $instance
     * @param  string $classOrAlias
     * @param  array  $paramValues
     * @return string
     */
    protected function createHashForValues( $classOrAlias, $paramValues ) {
        $hashValue = '';
        foreach ($paramValues as $param) {
            switch (gettype($param)) {
                case 'object':
                    $hashValue .= spl_object_hash($param) . '|';
                    break;
                case 'integer':
                case 'string':
                case 'boolean':
                case 'NULL':
                case 'double':
                    $hashValue .= $param . '|';
                    break;
                case 'array':
                    $hashValue .= 'Array|';
                    break;
                case 'resource':
                    $hashValue .= 'resource|';
                    break;
            }
        }
        return $hashValue;
    }

    /**
     * Есть ли в массиве объектов c параметрами $sharedInstancesWithParams объект класса $classOrAlias с параметрами $params
     * @param  string      $classOrAlias
     * @param  array       $params
     * @param  bool        $returnFastHashLookupKey
     * @return bool | string(hashKey.'/'.hashValue если $returnFastHashLookupKey=true)
     */
    function hasSharedInstanceWithParameters( $classOrAlias, array $params, $returnFastHashLookupKey = false ) {
        ksort($params);
        $hashKey = $this->createHashForKeys( $classOrAlias, array_keys($params) );
        if ( isset( $this->sharedInstancesWithParams['hashShort'][$hashKey] ) ) {
            $hashValue = $this->createHashForValues( $classOrAlias, $params );
            if ( isset( $this->sharedInstancesWithParams['hashLong'][$hashKey . '/' . $hashValue] ) ) {
                return ($returnFastHashLookupKey) ? $hashKey . '/' . $hashValue : true;
            }
        }
        return false;
    }

    /**
     * Добавить в массив объектов c параметрами $sharedInstancesWithParams объект $instance класса $classOrAlias с параметрами $params
     * @param  object $instance
     * @param  string $classOrAlias
     * @param  array  $params
     */
    public function addSharedInstanceWithParameters( $instance, $classOrAlias, array $params ) {
        ksort($params);
        $hashKey = $this->createHashForKeys( $classOrAlias, array_keys($params) );
        $hashValue = $this->createHashForValues( $classOrAlias, $params );

        if ( !isset($this->sharedInstancesWithParams[$hashKey]) || !is_array($this->sharedInstancesWithParams[$hashKey]) ) {
            $this->sharedInstancesWithParams[$hashKey] = array();
        }
        $this->sharedInstancesWithParams['hashShort'][$hashKey] = true;
        $this->sharedInstancesWithParams['hashLong'][$hashKey . '/' . $hashValue] = $instance;
    }

    /**
     * Вернуть из массива sharedInstanceWithParameters объект класса $classOrAlias с параметрами $params
     * @param  string      $classOrAlias
     * @param  array       $params
     * @param  string|null   $fastHashFromHasLookup  <- hasSharedInstanceWithParameters( $classOrAlias, $params, true )
     * @return object|bool false если объект был не найден
     */
    public function getSharedInstanceWithParameters( $classOrAlias, array $params, $fastHashFromHasLookup = null ) {
        // getSharedInstanceWithParameters( $classOrAlias, $params, hasSharedInstanceWithParameters( $classOrAlias, $params, true ) ) 
        // благодаря 3-му параметру true hasSharedInstanceWithParameters() возвращает string $fastHashFromHasLookup = hashKey.'/'.hashValue
        if ( $fastHashFromHasLookup ) {
            return $this->sharedInstancesWithParams['hashLong'][$fastHashFromHasLookup];
        }
        ksort($params);
        $hashKey = $this->createHashForKeys( $classOrAlias, array_keys($params) );
        if ( isset($this->sharedInstancesWithParams['hashShort'][$hashKey]) ) {
            $hashValue = $this->createHashForValues( $classOrAlias, $params );
            if ( isset($this->sharedInstancesWithParams['hashLong'][$hashKey . '/' . $hashValue]) ) {
                return $this->sharedInstancesWithParams['hashLong'][$hashKey . '/' . $hashValue];
            }
        }
        return false;
    }
    
    ////////////////////////////////////// aliases /////////////////////////////////////////////////
    /**
     * Есть ли альяс в массиве альясов
     * @param  string $alias
     * @return bool
     */
    public function hasAlias( $alias ) {
        return (isset($this->aliases[$alias]));
    }

    /**
     * Вернуть массив альясов
     * @return array
     */
    public function getAliases() {
        return $this->aliases;
    }

    /**
     * Вернуть название класса по альясу из массива альясов 
     * @param  string $alias
     * @return string | bool
     * @throws Exception\RuntimeException
     */
    public function getClassFromAlias( $alias ) {
        if (!isset($this->aliases[$alias])) {
            return false;
        }
        $r = 0;
        // рекурсивный поиск имени класса, если заданы несколько альясов для класса альяс1=>альяс2=>...=>альясN => class
        while (isset($this->aliases[$alias])) {
            $alias = $this->aliases[$alias];
            $r++;
            if ($r > 100) {
                throw new Exception\RuntimeException(
                    sprintf('Possible infinite recursion in DI alias! Max recursion of 100 levels reached at alias "%s".', $alias)
                );
            }
        }
        return $alias;
    }

    /**
     * Вернуть последний(базовый) альяс, если в массиве сохраненны несколько альясов для одного класса:
     *  альяс1=>альяс2=>...=>альясN => class
     * @param  string $alias
     * @return string|bool
     * @throws Exception\RuntimeException
     */
    protected function getBaseAlias($alias) {
        if (!$this->hasAlias($alias)) {
            return false;
        }
        $lastAlias = false;
        $r = 0;
        while (isset($this->aliases[$alias])) {
            $lastAlias = $alias;
            $alias = $this->aliases[$alias];
            $r++;
            if ($r > 100) {
                throw new Exception\RuntimeException(
                    sprintf('Possible infinite recursion in DI alias! Max recursion of 100 levels reached at alias "%s".', $alias)
                );
            }
        }
        return $lastAlias;
    }

    /**
     * Добавить пару альяс=>класс в массив альяс/классов
     * @throws Exception\InvalidArgumentException, если альяс не соответствует '#^[a-zA-Z0-9-_]+$#'
     * @param  string  $alias
     * @param  string  $class
     * @param  array   $parameters - параметры класса ['parameters' => $parameters] для передачи в setParameters($alias, $parameters)
     */
    public function addAlias( $alias, $class, array $parameters = array() ) {
        if (!preg_match('#^[a-zA-Z0-9-_]+$#', $alias)) {
            throw new Exception\InvalidArgumentException(
                'Aliases must be alphanumeric and can contain dashes and underscores only.'
            );
        }
        $this->aliases[$alias] = $class;
        if ($parameters) {
            $this->setParameters($alias, $parameters);
        }
    }

    ////////////////////////////////// configurations //////////////////////////////////////////////
    /**
     * Есть ли конфигурация для альяса/класса $aliasOrClass в массиве configurations[$aliasOrClass]
     * @param  string $aliasOrClass
     * @return bool
     */
    public function hasConfig( $aliasOrClass ) {
        $key = ($this->hasAlias($aliasOrClass)) ? 'alias:' . $this->getBaseAlias($aliasOrClass) : $aliasOrClass;
        if (!isset($this->configurations[$key])) {
            return false;
        }
        if ($this->configurations[$key] === $this->configurationTemplate) {
            return false;
        }
        return true;
    }
    
    /**
     * Получить конфигурацию для альяса/класса $aliasOrClass из массива configurations[$aliasOrClass]
     * @param  string $aliasOrClass
     * @return array
     */
    public function getConfig( $aliasOrClass ) {
        $key = ($this->hasAlias($aliasOrClass)) ? 'alias:' . $this->getBaseAlias($aliasOrClass) : $aliasOrClass;
        if (isset($this->configurations[$key])) {
            return $this->configurations[$key];
        }
        return $this->configurationTemplate;
    }
    
    /**
     * Задать конфигурацию для альяса/класса $aliasOrClass и поместить в массив конфигураций:
     *  configurations[$aliasOrClass] = [ 'parameters' => [], 'injections' => [], 'shared' => bool ]
     * @param string $aliasOrClass
     * @param array  $configuration
     * @param bool   $append
     */
    function setConfig( $aliasOrClass, array $configuration, $append = false ) {
        $key = ($this->hasAlias($aliasOrClass)) ? 'alias:' . $this->getBaseAlias($aliasOrClass) : $aliasOrClass;
        // 
        if ( !isset( $this->configurations[$key] ) || !$append ) {
            $this->configurations[$key] = $this->configurationTemplate;
        }
        // Игнорировать всё, кроме parameters, injections и shared
        $configuration = array(
            'parameters' => isset($configuration['parameters']) ? $configuration['parameters'] : array(),
            'injections' => isset($configuration['injections']) ? $configuration['injections'] : array(),
            'shared'     => isset($configuration['shared'])     ? $configuration['shared']     : true
        );
        $this->configurations[$key] = array_replace_recursive($this->configurations[$key], $configuration);
    }

    /**
	 * Задать параметры класса/альяса $aliasOrClass в массиве конфигураций:
	 *  configurations[$aliasOrClass] = [ 'parameters' => [], 'injections' => [], 'shared' => bool ]
     * @param string $aliasOrClass 
     * @param array  $parameters - ['parameters' => [] ]
     */
    function setParameters( $aliasOrClass, array $parameters ) {
        $this->setConfig($aliasOrClass, array('parameters' => $parameters), true);
    }

    /**
	 * Задать инжекции класса/альяса $aliasOrClass в массиве конфигураций:
     *  configurations[$aliasOrClass] = [ 'parameters' => [], 'injections' => [], 'shared' => bool ]
     * @param string $aliasOrClass
     * @param array  $injections - ['injections' => [] ]
     */
    function setInjections( $aliasOrClass, array $injections ) {
        $this->setConfig($aliasOrClass, array('injections' => $injections), true);
    }

    /**
     * Задать доступы класса/альяса $aliasOrClass в массиве конфигураций:
     *  configurations[$aliasOrClass] = [ 'parameters' => [], 'injections' => [], 'shared' => bool ]
     * @param  string $aliasOrClass
     * @param  bool   $isShared - доступ
     */
    public function setShared( $aliasOrClass, $isShared ) {
        $this->setConfig( $aliasOrClass, array('shared' => (bool)$isShared), true );
    }
 
    /**
	 * Вернуть все имена классов в виде числового массива $classes[] = $name из массива конфигураций configurations
     * @return array
     */
    public function getClasses() {
        $classes = array();
        foreach ($this->configurations as $name => $data) {
            if (strpos($name, 'alias') === 0) continue;
            $classes[] = $name;
        }
        return $classes;
    }
    
	//////////////////////////////////////// typePreferences ///////////////////////////////////////
    /**
     * Есть ли предпочтительный тип в массиве предпочтительных типов с ключом $interfaceOrAbstract
     * @param  string $interfaceOrAbstract
     * @return bool
     */
    public function hasTypePreferences( $interfaceOrAbstract ) {
        $key = ($this->hasAlias($interfaceOrAbstract)) ? 'alias:' . $interfaceOrAbstract : $interfaceOrAbstract;
        return (isset($this->typePreferences[$key]) && $this->typePreferences[$key]);
    }

    /**
     * Задать элементу массива предпочтительных типов с ключом интерфейса/абстракции массив реализаций $preferredImplementations
     *  typePreferences[$interfaceOrAbstract][...] => $preferredImplementations[...];
     * @param  string $interfaceOrAbstract
     * @param  array  $preferredImplementations
     * @return self
     */
    public function setTypePreference( $interfaceOrAbstract, array $preferredImplementations ) {
        $key = ($this->hasAlias($interfaceOrAbstract)) ? 'alias:' . $interfaceOrAbstract : $interfaceOrAbstract;
        foreach ($preferredImplementations as $preferredImplementation) {
            $this->addTypePreference($key, $preferredImplementation);
        }
        return $this;
    }

    /**
     * Получить массив предпочтительных реализаций для interfaceOrAbstract
     * @param  string $interfaceOrAbstract
     * @return array
     */
    public function getTypePreferences($interfaceOrAbstract) {
        $key = ($this->hasAlias($interfaceOrAbstract)) ? 'alias:' . $interfaceOrAbstract : $interfaceOrAbstract;
        if (isset($this->typePreferences[$key])) {
            return $this->typePreferences[$key];
        }
        return array();
    }

    /**
     * Удалить из массива предпочтительных типов все элементы $interfaceOrAbstract
     *	unset($this->typePreferences[$interfaceOrAbstract]
     * @param  string $interfaceOrAbstract
     */
    public function unsetTypePreferences($interfaceOrAbstract) {
        $key = ($this->hasAlias($interfaceOrAbstract)) ? 'alias:' . $interfaceOrAbstract : $interfaceOrAbstract;
        unset($this->typePreferences[$key]);
    }

    /**
     * Adds a type preference. A type preference is a redirection to a preferred alias or type when an abstract type
     * $interfaceOrAbstract is requested
	 * Предпочтительный тип это перенаправление к предпочтительному альясу или типу, когда запрашивается абстрактным тип $InterfaceOrAbstract
	 * Добавить в массив реализаций интерфейсов/абстракций предпочтительный тип
	 *  typePreferences[$interfaceOrAbstract][] = $preferredImplementation
     *
     * @param  string $interfaceOrAbstract
     * @param  string $preferredImplementation
     * @return self
     */
    public function addTypePreference( $interfaceOrAbstract, $preferredImplementation ) {
        $key = ( $this->hasAlias($interfaceOrAbstract) ) ? 'alias:' . $interfaceOrAbstract : $interfaceOrAbstract;
        if (!isset($this->typePreferences[$key])) {
            $this->typePreferences[$key] = array();
        }
        $this->typePreferences[$key][] = $preferredImplementation;
        return $this;
    }

    /**
     * Удалить из массива предпочтительных типов элемент конкретной реализации типа $preferredType
     *	typePreferences[$interfaceOrAbstract][...] => $preferredType;
     * @param  string    $interfaceOrAbstract
     * @param  string    $preferredType
     * @return bool|self
     */
    function removeTypePreference($interfaceOrAbstract, $preferredType) {
        $key = ($this->hasAlias($interfaceOrAbstract)) ? 'alias:' . $interfaceOrAbstract : $interfaceOrAbstract;
        if (!isset($this->typePreferences[$key]) || !in_array($preferredType, $this->typePreferences[$key])) {
            return false;
        }
        unset($this->typePreferences[$key][array_search($key, $this->typePreferences)]);
        return $this;
    }
}
