<?php

namespace MysqlGenerator\Sql\Column;

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
    public function __construct($name, $precision, $scale = null){
        $this->name      = $name;
        $this->precision = $precision;
        $this->scale     = $scale;
    }

    /**
     * @return array
     */
    public function getExpressionData(){
		
        $spec   = $this->specification;
		
        $params = array();
		$params[] = $this->name;
        $params[] = $this->precision;
		$params[] = (!$this->isNullable) ? 'NOT NULL' : '';
		$params[] = ($this->default !== null) ? $this->default : '';
		
        $types    = array(self::TYPE_IDENTIFIER, self::TYPE_LITERAL);
        $types[]  = self::TYPE_LITERAL;
		$types[]  = ($this->default !== null) ? self::TYPE_VALUE : self::TYPE_LITERAL;

        if ($this->scale !== null) {
            $params[1] .= ', ' . $this->scale;
        }
       
        return array(array(
            $spec,
            $params,
            $types,
        ));
    }
}
