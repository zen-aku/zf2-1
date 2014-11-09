<?php

namespace MysqlGenerator\Sql;

interface ExpressionInterface {
	
    const TYPE_IDENTIFIER = 'identifier';
    const TYPE_VALUE = 'value';
    const TYPE_LITERAL = 'literal';

    /**
     * @abstract
     * @return array of array|string should return an array in the format:
     * array (
     *    string $specification,// a sprintf formatted string   
     *    array $values,		// the values for the above sprintf formatted string    
     *    array $types,			// an array of equal length of the $values array, with either TYPE_IDENTIFIER or TYPE_VALUE for each value
     * )
     */
    public function getExpressionData();
}
