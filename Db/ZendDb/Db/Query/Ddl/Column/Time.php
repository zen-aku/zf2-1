<?php

namespace Zend\Db\Query\Ddl\Column;

/**
 * 
 */
class Time extends Column {
    /**
     * @var string
     */
    protected $specification = '%s TIME %s %s';

    /**
     * @param string $name
     */
    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getExpressionData() {
        $spec   = $this->specification;
        $params = array();

        $types    = array(self::TYPE_IDENTIFIER);
        $params[] = $this->name;

        $types[]  = self::TYPE_LITERAL;
        $params[] = (!$this->isNullable) ? 'NOT NULL' : '';

        $types[]  = ($this->default !== null) ? self::TYPE_VALUE : self::TYPE_LITERAL;
        $params[] = ($this->default !== null) ? $this->default : '';

        return array(array(
            $spec,
            $params,
            $types,
        ));
    }
}