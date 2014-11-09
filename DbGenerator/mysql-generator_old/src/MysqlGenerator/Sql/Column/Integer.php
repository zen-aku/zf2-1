<?php

namespace MysqlGenerator\Sql\Column;

class Integer extends Column {
	
    /**
     * @var int
     */
    protected $length;

    /**
     * @param null|string     $name
     * @param bool            $nullable
     * @param null|string|int $default
     * @param array           $options
     */
    public function __construct($name, $nullable = false, $default = null, array $options = array()) {
        $this->setName($name);
        $this->setNullable($nullable);
        $this->setDefault($default);
        $this->setOptions($options);
    }
}
