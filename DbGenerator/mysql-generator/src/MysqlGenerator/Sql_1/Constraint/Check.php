<?php

namespace MysqlGenerator\Sql\Constraint;

class Check extends AbstractConstraint {
	
    /**
     * @var string|\MysqlGenerator\Sql\ExpressionInterface
     */
    protected $expression;

    /**
     * @var string
     */
    protected $specification = 'CONSTRAINT %s CHECK (%s)';

    /**
     * @param  string|\MysqlGenerator\Sql\ExpressionInterface $expression
     * @param  null|string $name
     */
    public function __construct($expression, $name){
        $this->expression = $expression;
        $this->name       = $name;
    }

    /**
     * @return array
     */
    public function getExpressionData(){
        return array(array(
            $this->specification,
            array($this->name, $this->expression),
            array(self::TYPE_IDENTIFIER, self::TYPE_LITERAL),
        ));
    }
}
