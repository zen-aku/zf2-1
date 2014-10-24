<?php

namespace Zend\Db\Query\Ddl\Constraint;

/**
 * 
 */
class PrimaryKey extends AbstractConstraint {
    
    /**
     * @var string
     */
    protected $specification = 'PRIMARY KEY (%s)';

    /**
     * @return array
     */
    public function getExpressionData() {
        $colCount     = count($this->columns);
        $newSpecParts = array_fill(0, $colCount, '%s');
        $newSpecTypes = array_fill(0, $colCount, self::TYPE_IDENTIFIER);

        $newSpec = sprintf($this->specification, implode(', ', $newSpecParts));

        return array(array(
            $newSpec,
            $this->columns,
            $newSpecTypes,
        ));
    }
}
