<?php

namespace MysqlGenerator\Sql\Column;

class Boolean extends Column {
	
    /**
     * @var string specification
     */
    protected $specification = '%s TINYINT NOT NULL';

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
        $params = array($this->name);
        $types  = array(self::TYPE_IDENTIFIER);

        return array(array(
            $spec,
            $params,
            $types,
        ));
    }
}
