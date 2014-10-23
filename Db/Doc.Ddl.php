<?php
Классы Zend\Db\Sql\Ddl\ формируют строку Ddl-запроса.
Для выполнения сформированного Ddl-запроса надо воспользоваться методом класса Zend\Db\Sql\Sql::getSqlStringForSqlObject($ddl):
    $adapter = new \Zend\Db\Adapter\Adapter($array_connectDb)
    $sql = new \Zend\Db\Sql\Sql($adapter);
    $query = $sql->getSqlStringForSqlObject($ddl);
	$sql->getAdapter()->query($query,'execute');
    // В одну строку:
    $sql->getAdapter()->query($sql->getSqlStringForSqlObject($ddl),'execute');
  
В API CreateTable не предусмотрено задание engine таблицы. Надо сделать, чтобы это было возможным.  
Непонятно для чего нужен массив-параметр $options конструкторов Column\ . Не нашёл его использование.
Непонятно как задавать дополнительные параметры колонкам, напр. AUTO_INCREMENT или UNSIGNED.
API DDL производит впечатление недоделанности и забагованности(класс Sql\Insert). Он нуждается в глубоком допиливании.    
-------------------
    API DDL:
-------------------    
class DropTable extends AbstractSql implements SqlInterface {
    protected $specifications = array(
        self::TABLE => 'DROP TABLE %1$s' // изменить на 'DROP TABLE IF EXISTS %1$s'
    );
    const TABLE = 'table'
    __construct($table = '')
    getSqlString(PlatformInterface $adapterPlatform = null)       
} 

class CreateTable extends AbstractSql implements SqlInterface {
    protected $specifications = array(
        self::TABLE => 'CREATE %1$sTABLE %2$s ('
    const COLUMNS     = 'columns';
    const CONSTRAINTS = 'constraints';
    const TABLE       = 'table';  
    __construct($table = '', $isTemporary = false)
    setTemporary($temporary)
    isTemporary()  
    setTable($name)
    addColumn(Column\ColumnInterface $column)
    addConstraint(Constraint\ConstraintInterface $constraint) 
    getRawState($key = null)
    getSqlString(PlatformInterface $adapterPlatform = null)    
}

class AlterTable extends AbstractSql implements SqlInterface {
    protected $specifications = array(
        self::TABLE => "ALTER TABLE %1\$s\n"
    const ADD_COLUMNS      = 'addColumns';
    const ADD_CONSTRAINTS  = 'addConstraints';
    const CHANGE_COLUMNS   = 'changeColumns';
    const DROP_COLUMNS     = 'dropColumns';
    const DROP_CONSTRAINTS = 'dropConstraints';
    const TABLE            = 'table';
    __construct($table = '')
    setTable($name)    
    addColumn(Column\ColumnInterface $column)
    changeColumn($name, Column\ColumnInterface $column)
    dropColumn($name)
    dropConstraint($name)
    addConstraint(Constraint\ConstraintInterface $constraint)  
    getRawState($key = null)
    getSqlString(PlatformInterface $adapterPlatform = null)    
}

----------------------------------------------------------------
API Colum CreateTable::addColumn(Column\ColumnInterface $column)
----------------------------------------------------------------
class Column implements ColumnInterface {
    __construct($name = null)
    setName($name)
    getName() 
    setNullable($nullable) 
    isNullable()
    setDefault($default)
    getDefault() 
    setOptions(array $options)
    setOption($name, $value)
    getOptions()
    getExpressionData()    
}

//////// Числа
class Boolean extends Column {
    protected $specification = '%s TINYINT NOT NULL';
    __construct($name)
    getExpressionData()   
}
class Integer extends Column {
    __construct($name, $nullable = false, $default = null, array $options = array())   
}
class BigInteger extends Integer {  
    protected $type = 'BIGINT';
}
class Decimal extends Column {
    protected $specification = '%s DECIMAL(%s) %s %s';
    __construct($name, $precision, $scale = null)
    getExpressionData()    
}
class Float extends Column {
    protected $specification = '%s DECIMAL(%s) %s %s';
    __construct($name, $digits, $decimal)
    getExpressionData()    
}

////////// Строки
class Blob extends Column {
    protected $type = 'BLOB';
    __construct($name, $length, $nullable = false, $default = null, array $options = array())
    setLength($length)
    getLength()
    getExpressionData()
}
class Char extends Column {
    protected $specification = '%s CHAR(%s) %s %s';
    __construct($name, $length)
    getExpressionData()    
}
class Varchar extends Column {
    protected $specification = '%s VARCHAR(%s) %s %s';
    __construct($name, $length)
    getExpressionData()    
}
class Text extends Column {
    protected $specification = '%s TEXT %s %s';
    __construct($name)
    getExpressionData()
}

/////////// Время
class Date extends Column {
    protected $specification = '%s DATE %s %s';
    __construct($name)
    getExpressionData()
}
class Time extends Column {
    protected $specification = '%s TIME %s %s';
    __construct($name)
    getExpressionData()
}

-------------------------------------------------------------------------------------
API Constraint CreateTable::addConstraint(Constraint\ConstraintInterface $constraint)
-------------------------------------------------------------------------------------
abstract class AbstractConstraint implements ConstraintInterface{
    __construct($columns = null)
    setColumns($columns)    
    addColumn($column)
    getColumns()    
}
class PrimaryKey extends AbstractConstraint {
    protected $specification = 'PRIMARY KEY (%s)';
    getExpressionData()   
}
class UniqueKey extends AbstractConstraint {
    protected $specification = 'CONSTRAINT UNIQUE KEY %s(...)';
    __construct($column, $name = null)
    getExpressionData()    
}
class ForeignKey extends AbstractConstraint {
    protected $specification = 'CONSTRAINT %1$s FOREIGN KEY (%2$s) REFERENCES %3$s (%4$s) ON DELETE %5$s ON UPDATE %6$s';
    __construct($name, $column, $referenceTable, $referenceColumn, $onDeleteRule = null, $onUpdateRule = null)
    setName($name)
    getName()
    setReferenceTable($referenceTable)
    getReferenceTable()
    setReferenceColumn($referenceColumn)
    getReferenceColumn()
    setOnDeleteRule($onDeleteRule)
    getOnDeleteRule()
    setOnUpdateRule($onUpdateRule)
    getOnUpdateRule()
    getExpressionData()    
}
    
В API Constraint не предусмотрено задание имени сonstraint. 
Надо изменить напр protected $specification = 'CONSTRAINT UNIQUE KEY %s(...)' на
protected $specification = 'CONSTRAINT %s UNIQUE KEY %s(...)' и добавить изменения в методах чтобы
была возможность задания имени сonstraint.    
