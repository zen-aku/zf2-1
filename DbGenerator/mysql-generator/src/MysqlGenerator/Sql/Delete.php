<?php

namespace MysqlGenerator\Sql;

use MysqlGenerator\Adapter\AdapterInterface;
use MysqlGenerator\Adapter\ParameterContainer;
use MysqlGenerator\Adapter\StatementContainerInterface;

/**
 *
 * @property Where $where
 */
class Delete extends AbstractSql implements SqlInterface, PreparableSqlInterface {
	
    /**
     * @const
     */
    const SPECIFICATION_DELETE = 'delete';
    const SPECIFICATION_WHERE = 'where';

    /**
     * @var array Specifications
     */
    protected $specifications = array(
        self::SPECIFICATION_DELETE => 'DELETE FROM %1$s',
        self::SPECIFICATION_WHERE => 'WHERE %1$s'
    );
			
    /**
     * @var string|TableIdentifier
     */
    protected $table = '';

    /**
     * @var bool
     */
    protected $emptyWhereProtection = true;

    /**
     * @var array
     */
    protected $set = array();

    /**
     * @var null|string|Where
     */
    protected $where = null;

    /**
     * Constructor
     * @param  null|string|TableIdentifier $table
     */
    public function __construct( $table = null ){
        $this->table = $table;
        $this->where = new Where();
    }

    /**
     * Create from statement
     * @param  string|TableIdentifier $table
     * @return Delete
     */
    public function from($table){
        $this->table = $table;
        return $this;
    }

	/**
	 * @param string $key
	 * @return array
	 */
    public function getRawState($key = null){
        $rawState = array(
            'emptyWhereProtection' => $this->emptyWhereProtection,
            'table' => $this->table,
            'set' => $this->set,
            'where' => $this->where
        );
        return (isset($key) && array_key_exists($key, $rawState)) ? $rawState[$key] : $rawState;
    }

    /**
     * Create where clause
     * @param  Where|\Closure|string|array $predicate
     * @param  string $combination One of the OP_* constants from Predicate\PredicateSet
     * @return Delete
     */
    public function where($predicate, $combination = Predicate\PredicateSet::OP_AND){
        if ($predicate instanceof Where) {
            $this->where = $predicate;
        } else {
            $this->where->addPredicates($predicate, $combination);
        }
        return $this;
    }

    /**
     * Prepare the delete statement
     * @param  AdapterInterface $adapter
     * @param  StatementContainerInterface $statementContainer
     * @return void
     */
    public function prepareStatement(AdapterInterface $adapter, StatementContainerInterface $statementContainer){
         	
        $sql = sprintf(
			$this->specifications[static::SPECIFICATION_DELETE], 
			$this->getQuoteSchemaTable()							// " `schema`.`table` "
		);
        if ($this->where->count() > 0) {
            $whereParts = $this->processExpression($this->where, $adapter, true, 'where');
            $statementContainer->getParameterContainer()->merge($whereParts->getParameterContainer());
            $sql .= ' ' . sprintf(
				$this->specifications[static::SPECIFICATION_WHERE], 
				$whereParts->getSql()
			);
        }
        $statementContainer->setSql($sql);
    }

    /**
     * Get the SQL string
     * @param AdapterInterface $adapter
     * @return string
     */
    public function getSqlString(AdapterInterface $adapter){
       	
        $sql = sprintf(
			$this->specifications[static::SPECIFICATION_DELETE], 
			$this->getQuoteSchemaTable()							// " `schema`.`table` "
		);
        if ($this->where->count() > 0) {
            $whereParts = $this->processExpression($this->where, $adapter, false, 'where');
            $sql .= ' ' . sprintf($this->specifications[static::SPECIFICATION_WHERE], $whereParts->getSql());
        }
        return $sql;
    }

    /**
     * Property overloading
     * Overloads "where" only.
     * @param  string $name
     * @return mixed
     */
    public function __get($name){
        switch (strtolower($name)) {
            case 'where':
                return $this->where;
        }
    }
}
