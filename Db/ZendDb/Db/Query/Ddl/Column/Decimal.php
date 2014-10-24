<?php

namespace Zend\Db\Query\Ddl\Column;

/**
 * 
 */
class Decimal extends Column {
    
    /**
     * @var int
     */
    protected $precision;

    /**
     * @var int
     */
    protected $scale;

    /**
     * @var string
     */
    protected $specification = '%s DECIMAL(%s) %s %s';

    /**
     * @param null|string $name
     * @param int $precision
     * @param null|int $scale
     */
    public function __construct($name, $precision, $scale = null) {
        $this->name      = $name;
        $this->precision = $precision;
        $this->scale     = $scale;
    }

    /**
     * @return array
     */
    public function getExpressionData() {
        $spec   = $this->specification;
        $params = array();

        $types    = array(self::TYPE_IDENTIFIER, self::TYPE_LITERAL);
        $params[] = $this->name;
        $params[] = $this->precision;

        if ($this->scale !== null) {
            $params[1] .= ', ' . $this->scale;
        }

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
