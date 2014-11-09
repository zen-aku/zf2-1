<?php

namespace MysqlGenerator\Sql\Column;

class Text extends Column {
	
    /**
     * @var string
     */
    protected $specification = '%s TEXT %s %s';

    /**
     * @param null|string $name
     */
    public function __construct($name){
        $this->name   = $name;
    }

    /**
     * @return array
     */
    public function getExpressionData() {
		
        $spec   = $this->specification;
		
        $params = array();
		$params[] = $this->name;
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
