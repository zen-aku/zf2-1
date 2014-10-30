<?php

namespace MysqlGenerator\Sql\Ddl\Column;

class Varchar extends Column
{
    /**
     * @var int
     */
    protected $length;

    /**
     * @var string
     */
    protected $specification = '%s VARCHAR(%s) %s %s';

    /**
     * @param null|string $name
     * @param int $length
     */
    public function __construct($name, $length)
    {
        $this->name   = $name;
        $this->length = $length;
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
        $params[] = $this->length;

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
