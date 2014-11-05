<?php

namespace MysqlGenerator\Sql\Column;

class Date extends Column {
	
    /**
     * @var string
     */
    protected $specification = '%s DATE %s %s';

    /**
     * @param string $name
     */
    public function __construct($name){
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getExpressionData(){
		
        $spec   = $this->specification;
		
        $params = array();
		$params[] = $this->name;
		$params[] = (!$this->isNullable) ? 'NOT NULL' : '';
		$params[] = ($this->default !== null) ? $this->default : '';

        $types    = array(self::TYPE_IDENTIFIER);       
        $types[]  = self::TYPE_LITERAL;      
        $types[]  = ($this->default !== null) ? self::TYPE_VALUE : self::TYPE_LITERAL;
        
        return array(array(
            $spec,
            $params,
            $types,
        ));
    }
}
