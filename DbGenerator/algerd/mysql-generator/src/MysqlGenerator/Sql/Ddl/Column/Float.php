<?php

namespace MysqlGenerator\Sql\Ddl\Column;

class Float extends Column
{
    /**
     * @var int
     */
    protected $decimal;

    /**
     * @var int
     */
    protected $digits;

    /**
     * @var string
     */
    protected $specification = '%s DECIMAL(%s) %s %s';

    /**
     * @param null|string $name
     * @param int $digits
     * @param int $decimal
     */
    public function __construct($name, $digits, $decimal)
    {
        $this->name    = $name;
        $this->digits  = $digits;
        $this->decimal = $decimal;
    }

    /**
     * @return array
     */
    public function getExpressionData()
    {
        $spec   = $this->specification;
        $params = array();

        $types      = array(self::TYPE_IDENTIFIER, self::TYPE_LITERAL);
        $params[]   = $this->name;
        $params[]   = $this->digits;
        $params[1] .= ', ' . $this->decimal;

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
