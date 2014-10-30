<?php

namespace MysqlGenerator\Sql\Ddl\Column;

class Text extends Column
{
    /**
     * @var string
     */
    protected $specification = '%s TEXT %s %s';

    /**
     * @param null|string $name
     */
    public function __construct($name)
    {
        $this->name   = $name;
    }

    /**
     * @return array
     */
    public function getExpressionData()
    {
        $spec   = $this->specification;
        $params = array();

        $types    = array(self::TYPE_IDENTIFIER, self::TYPE_LITERAL);
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
