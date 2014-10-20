<?php

Zend\Db\Sql\Sql формирует строку Sql-запроса в соответствии с вызванными методами класса Zend\Db\Sql\Sql.
Для небольших запросов этот класс предоставляет неплохую гибкость, напр. можно вконструкторе модели таблицы
создать свойства-объекты Select, Insert, Update, Delete для данной таблицы, а потом в соответствующих методах модели
обращаться к этим свойствам для создания конкретных запросов.
Но, для больших запросов с JOIN, вложенными подзапросами, сложными Where, группировками и т.д. код 
существенно утяжеляется, теряет читабельность, усложняется как внаписании так и в понимании.
В таких ситуациях целесообразно ипользовать подготовленные запросы с прямым Sql-кодом 
		или рассмотреть использование запросов c помощью Doctrine2
				
API Sql-запросов:

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
}

// Для детального WHERE:
class Predicate extends PredicateSet
{
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
    getExpressionData();
    count();
}

Некоторые методы класса Predicate можно заменить классами-командами из директории Zend\Db\Sql\Predicate:
Between, Expression, In, IsNotNull, isNull, Like, Literal, NotIn, NotKike, Operator (=,>,< и т.д.), но это утяжелит код.
Рекомендуется для сложных запросов Where применять класс Predicate и при необходимости дополнять его классами-командами из директории Zend\Db\Sql\Predicate		