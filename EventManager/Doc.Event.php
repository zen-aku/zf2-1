<?php
//namespace Zend\EventManager;
//use ArrayAccess;
/**
 * Весь Функционал объекта события класса Event, используемого в callback-слушателях
 * Объект Event инкапсулирует параметры события и внедряется в callback-слушателей через EventManager->trigger()
 */
interface EventInterface {
	/**
	 * Создать объект события и задать ему параметры
	 * @param  string $name Event name
	 * @param  string|object $target
	 * @param  array|ArrayAccess $params
	 */
	//function __construct($name = null, $target = null, $params = null);
	/**
	 * Вернуть имя собятия
	 * @return string
	 */
	function getName();
	/**
	 * Вернуть контекст (объект), из которого событие было запущено (triggered)
	 * @return null|string|object
	 */
	function getTarget();
	/**
	 * Вернуть параметры, переданныы в объект Event
	 * @return array|ArrayAccess
	 */
	function getParams();
	/**
	 * Вернуть параметр с именем $name. Если его не существует вернуть значение $default
	 * @param  string $name
	 * @param  mixed $default
	 * @return mixed
	 */
	function getParam($name, $default = null);
	/**
	 * Задать имя события
	 * @param  string $name
	 * @return $this
	 */
	function setName($name);
	/**
	 * Задать контекст (объект), из которого событие будет запущено (triggered)
	 * @param  null|string|object $target
	 * @return $this
	 *
	 */
	function setTarget($target);
	/**
	 * Задать параметры
	 * @param  array|ArrayAccess|object  $params
	 * @return $this
	 */
	function setParams($params);
	/**
	 * Задать параметр с ключом $name => $value
	 * @param  string $name
	 * @param  mixed $value
	 * @return $this
	 */
	function setParam($name, $value);
	/**
	 * Остановить или нет дальнейшее итерирование события в EventManager->trigger()
	 * @param  bool $flag
	 */
	function stopPropagation($flag = true);
	/**
	 * Итерирование события в EventManager->trigger() остановлено?
	 * @return bool
	 */
	function propagationIsStopped();
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Инкапсулирует параметры события и внедряется в callback-слушателей через EventManager->trigger()
 */
class Event implements EventInterface {

	protected $name;					// string Event name
	protected $target;					// string|object Может быть любой объект или имя статического метода
	protected $params = array();		// array|ArrayAccess|object The event parameters
	protected $stopPropagation = false; // bool Whether or not to stop propagation

	/**
	 * Accept a target and its parameters.
	 * @param  string $name Event name
	 * @param  string|object $target
	 * @param  array|ArrayAccess $params
	 */
	function __construct($name = null, $target = null, $params = null) {
		if (null !== $name) {
			$this->setName($name);
		}
		if (null !== $target) {
			$this->setTarget($target);
		}
		if (null !== $params) {
			$this->setParams($params);
		}
	}

	/**
	 * Вернуть имя собятия
	 * @return string
	 */
	function getName() {
		return $this->name;
	}

	/**
	 * Вернуть контекст (объект), из которого событие было запущено (triggered)
	 * @return null|string|object
	 */
	function getTarget() {
		return $this->target;
	}

	/**
	 * Вернуть параметры, переданныы в объект Event
	 * @return array|ArrayAccess
	 */
	function getParams() {
		return $this->params;
	}

	/**
	 * Вернуть параметр с именем $name. Если его не существует вернуть значение $default
	 * @param  string $name
	 * @param  mixed $default
	 * @return mixed
	 */
	function getParam($name, $default = null) {
		// Check in params that are arrays or implement array access
		if (is_array($this->params) || $this->params instanceof ArrayAccess) {
			if (!isset($this->params[$name])) {
				return $default;
			}
			return $this->params[$name];
		}
		// Check in normal objects
		if (!isset($this->params->{$name})) {
			return $default;
		}
		return $this->params->{$name};
	}

	/**
	 * Задать имя события
	 * @param  string $name
	 * @return $this
	 */
	function setName($name) {
		$this->name = (string) $name;
		return $this;
	}

	/**
	 * Задать контекст (объект), из которого событие будет запущено (triggered)
	 * @param  null|string|object $target
	 * @return $this
	 *
	 */
	function setTarget($target) {
		$this->target = $target;
		return $this;
	}

	/**
	 * Задать параметры
	 * @param  array|ArrayAccess|object  $params
	 * @return $this
	 */
	function setParams($params) {
		if (!is_array($params) && !is_object($params)) {
			throw new Exception\InvalidArgumentException(
					sprintf('Event parameters must be an array or object; received "%s"', gettype($params))
			);
		}
		$this->params = $params;
		return $this;
	}

	/**
	 * Задать параметр с ключом $name => $value
	 * @param  string $name
	 * @param  mixed $value
	 * @return $this
	 */
	function setParam($name, $value) {
		if (is_array($this->params) || $this->params instanceof ArrayAccess) {
			// Arrays or objects implementing array access
			$this->params[$name] = $value;
		} else {
			// Objects
			$this->params->{$name} = $value;
		}
		return $this;
	}

	/**
	 * Указывает EventManager->trigger() остановить или нет дальнейшее итерирование события
	 * @param  bool $flag
	 */
	function stopPropagation($flag = true) {
		$this->stopPropagation = (bool) $flag;
	}

	/**
	 * Итерирование события в EventManager->trigger() остановлено?
	 * @return bool
	 */
	function propagationIsStopped() {
		return $this->stopPropagation;
	}
}







