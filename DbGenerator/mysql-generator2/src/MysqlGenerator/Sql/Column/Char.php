<?php

namespace MysqlGenerator\Sql\Column;

class Char extends Column {
	
    /**
     * @var string
     */
    protected $specification = '%s CHAR(%s) %s %s';

    /**
     * @var int
     */
    protected $length;

    /**
     * @param string $name
     * @param int $length
     */
    public function __construct($name, $length){
        $this->name   = $name;
        $this->length = $length;
    }

    /**
     * @return array
     */
    public function getExpressionData() {
		
        $spec   = $this->specification;
		
        $params = array();
		$params[] = $this->name;
        $params[] = $this->length;
		$params[] = (!$this->isNullable) ? 'NOT NULL' : '';
		$params[] = ($this->default !== null) ? $this->default : '';
		
        $types    = array(self::TYPE_IDENTIFIER, self::TYPE_LITERAL);      
        $types[]  = self::TYPE_LITERAL;       
        $types[]  = ($this->default !== null) ? self::TYPE_VALUE : self::TYPE_LITERAL;
        
        return array(array(
            $spec,
            $params,
            $types,
        ));
    }
}
