<?php

Zend\Db\Sql\Sql формирует строку Sql-запроса в соответствии с вызванными методами класса Zend\Db\Sql\Sql.
Для небольших запросов этот класс предоставляет неплохую гибкость, напр. можно вконструкторе модели таблицы
создать свойства-объекты Select, Insert, Update, Delete для данной таблицы, а потом в соответствующих методах модели
обращаться к этим свойствам для создания конкретных запросов.
Но, для больших запросов с JOIN, вложенными подзапросами, сложными Where, группировками и т.д. код 
существенно утяжеляется, теряет читабельность, усложняется как внаписании так и в понимании.
В таких ситуациях целесообразно ипользовать подготовленные запросы с прямым Sql-кодом 
		или рассмотреть использование запросов c помощью Doctrine2
-------------------				
    API Sql:
-------------------
class Sql {
	__construct(AdapterInterface $adapter, $table = null, Platform\AbstractPlatform $sqlPlatform = null)
	AdapterInterface getAdapter()
	hasTable()
	setTable($table)		
	string getTable()
	getSqlPlatform()
	Select select($table = null)
	Insert insert($table = null)
	Update update($table = null)		
	Delete delete($table = null)
	prepareStatementForSqlObject(PreparableSqlInterface $sqlObject, StatementInterface $statement = null)		
	getSqlStringForSqlObject(SqlInterface $sqlObject, PlatformInterface $platform = null)		
}

class Select extends AbstractSql implements SqlInterface, PreparableSqlInterface {
	const JOIN_INNER = 'inner';
	const JOIN_OUTER = 'outer';
	const JOIN_LEFT = 'left';
	const JOIN_RIGHT = 'right';
	const SQL_STAR = '*';
	const ORDER_ASCENDING = 'ASC';
	const ORDER_DESCENDING = 'DESC';

	public $where; // @param Where $where

	__construct($table = null);
	from($table);
	columns(array $columns, $prefixColumnsWithTable = true);
	join($name, $on, $columns = self::SQL_STAR, $type = self::JOIN_INNER);
	where($predicate, $combination = Predicate\PredicateSet::OP_AND);
	group($group);
	having($predicate, $combination = Predicate\PredicateSet::OP_AND);
	order($order);
	limit($limit);
	offset($offset);
    prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer);
    getSqlString(PlatformInterface $adapterPlatform = null)
}

class Insert implements SqlInterface, PreparableSqlInterface {
    protected $specifications = array(
        self::SPECIFICATION_INSERT => 'INSERT INTO %1$s (%2$s) VALUES (%3$s)',
        self::SPECIFICATION_SELECT => 'INSERT INTO %1$s %2$s %3$s',
    );
    const VALUES_MERGE = 'merge';
    const VALUES_SET   = 'set';

    __construct($table = null);
    into($table);
    columns(array $columns);
    values(array $values, $flag = self::VALUES_SET);
    prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer) 
    getSqlString(PlatformInterface $adapterPlatform = null) 
}

class Update extends AbstractSql implements SqlInterface, PreparableSqlInterface {
    protected $specifications = array(
        self::SPECIFICATION_UPDATE => 'UPDATE %1$s SET %2$s',
        self::SPECIFICATION_WHERE => 'WHERE %1$s'
    );
    const VALUES_MERGE = 'merge';
    const VALUES_SET   = 'set';

    public $where; // @param Where $where
    __construct($table = null);
    table($table);
    set(array $values, $flag = self::VALUES_SET);
    where($predicate, $combination = Predicate\PredicateSet::OP_AND);
    prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer) 
    getSqlString(PlatformInterface $adapterPlatform = null) 
}

class Delete {
    protected $specifications = array(
        self::SPECIFICATION_DELETE => 'DELETE FROM %1$s',
        self::SPECIFICATION_WHERE => 'WHERE %1$s'
    );
    public $where; // @param Where $where
    __construct($table = null);
    from($table);
    where($predicate, $combination = Predicate\PredicateSet::OP_AND);
    prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer) 
    getSqlString(PlatformInterface $adapterPlatform = null) 
}

class Expression implements ExpressionInterface {
    const PLACEHOLDER = '?';
    __construct($expression = '', $parameters = null, array $types = array())
    setExpression($expression)
    getExpression()
    setParameters($parameters)    
    getParameters()
    setTypes(array $types)    
    getTypes()
    array getExpressionData()          
}

class Literal implements ExpressionInterface {
    __construct($literal = '')
    setLiteral($literal)     
    getLiteral()
    array getExpressionData()           
}

--------------------------
    API Predicate 
--------------------------
class PredicateSet implements PredicateInterface, Countable {
    const COMBINED_BY_AND = 'AND';
    const OP_AND          = 'AND';
    const COMBINED_BY_OR  = 'OR';
    const OP_OR           = 'OR';
    __construct(array $predicates = null, $defaultCombination = self::COMBINED_BY_AND)
    addPredicate(PredicateInterface $predicate, $combination = null)
    addPredicates($predicates, $combination = self::OP_AND)    
    getPredicates() 
    orPredicate(PredicateInterface $predicate)   
    andPredicate(PredicateInterface $predicate)
    array getExpressionData()
    count()    
}

class Predicate extends PredicateSet {
    public $and;
    public $or;
    public $AND;
    public $OR;
    public $NEST;
    public $UNNEST;

    nest();
    setUnnest(Predicate $predicate);
    unnest();
    equalTo($left, $right, $leftType = self::TYPE_IDENTIFIER, $rightType = self::TYPE_VALUE);
    lessThan($left, $right, $leftType = self::TYPE_IDENTIFIER, $rightType = self::TYPE_VALUE);
    greaterThan($left, $right, $leftType = self::TYPE_IDENTIFIER, $rightType = self::TYPE_VALUE);
    lessThanOrEqualTo($left, $right, $leftType = self::TYPE_IDENTIFIER, $rightType = self::TYPE_VALUE);
    greaterThanOrEqualTo($left, $right, $leftType = self::TYPE_IDENTIFIER, $rightType = self::TYPE_VALUE);
    like($identifier, $like);
    literal($literal);
    expression($expression, $parameter);
    isNull($identifier);
    isNotNull($identifier);
    in($identifier, array $valueSet = array());
    between($identifier, $minValue, $maxValue);
  // Inherited From PredicateSet
    addPredicate(PredicateInterface $predicate, $combination = null);
    getPredicates();
    orPredicate(PredicateInterface $predicate);
    andPredicate(PredicateInterface $predicate);
    array getExpressionData();
    count();
}

class Where extends Predicate\Predicate { 
}

class Having extends Predicate\Predicate {
}

class Expression extends BaseExpression implements PredicateInterface{
    __construct($expression = null, $valueParameter = null /*[, $valueParameter, ... ]*/)
}
--------------------------
    API Predicate-Command 
--------------------------       
class Operator implements PredicateInterface {
    const OPERATOR_EQUAL_TO                  = '=';
    const OP_EQ                              = '=';
    const OPERATOR_NOT_EQUAL_TO              = '!=';
    const OP_NE                              = '!=';
    const OPERATOR_LESS_THAN                 = '<';
    const OP_LT                              = '<';
    const OPERATOR_LESS_THAN_OR_EQUAL_TO     = '<=';
    const OP_LTE                             = '<=';
    const OPERATOR_GREATER_THAN              = '>';
    const OP_GT                              = '>';
    const OPERATOR_GREATER_THAN_OR_EQUAL_TO  = '>=';
    const OP_GTE                             = '>=';
    __construct($left = null, $operator = self::OPERATOR_EQUAL_TO, $right = null, $leftType = self::TYPE_IDENTIFIER, $rightType = self::TYPE_VALUE)
    setLeft($left)    
    getLeft()    
    setLeftType($type)
    getLeftType()    
    setOperator($operator)
    getOperator()    
    setRight($value)
    getRight()    
    setRightType($type)
    getRightType()    
    array getExpressionData()    
} 

class Between implements PredicateInterface {
    protected $specification = '%1$s BETWEEN %2$s AND %3$s';
    __construct($identifier = null, $minValue = null, $maxValue = null)
    setIdentifier($identifier)
    getIdentifier()    
    setMinValue($minValue)
    getMinValue()    
    setMaxValue($maxValue)
    getMaxValue()    
    setSpecification($specification)
    getSpecification()    
    array getExpressionData()    
}

class In implements PredicateInterface {
    protected $specification = '%s IN %s';
    __construct($identifier = null, $valueSet = null)
    setIdentifier($identifier)    
    getIdentifier()
    setValueSet($valueSet)    
    getValueSet()
    array getExpressionData()           
}

class IsNull implements PredicateInterface {
    protected $specification = '%1$s IS NULL';
    __construct($identifier = null)
    setIdentifier($identifier)    
    getIdentifier()
    setSpecification($specification)    
    getSpecification()
    array getExpressionData()    
}

class IsNotNull extends IsNull {
    protected $specification = '%1$s IS NOT NULL';
}

class Like implements PredicateInterface {
    protected $specification = '%1$s LIKE %2$s';
    __construct($identifier = null, $like = null)
    setIdentifier($identifier)
    getIdentifier()
    setLike($like)    
    getLike()    
    setSpecification($specification)
    getSpecification()  
    array getExpressionData()           
}

class NotIn extends In {
    protected $specification = '%s NOT IN %s';
}

class NotLike extends Like {
    protected $specification = '%1$s NOT LIKE %2$s';
}

class Literal extends BaseLiteral implements PredicateInterface { }


Некоторые методы класса Predicate можно заменить классами-командами из директории Zend\Db\Sql\Predicate:
Between, Expression, In, IsNotNull, isNull, Like, Literal, NotIn, NotKike, Operator (=,>,< и т.д.), но это утяжелит код.
Рекомендуется для сложных запросов Where применять класс Predicate и при необходимости дополнять его классами-командами из директории Zend\Db\Sql\Predicate	
    
    