<?php

namespace MysqlGenerator\Sql\Keyword;

use MysqlGenerator\Adapter\AdapterInterface;
use MysqlGenerator\Sql\SqlInterface;

class Join extends AbstractKeyword {
	
	const STRAIGHT_JOIN = 'STRAIGHT_JOIN';
	const JOIN = 'JOIN';
	const CROSS = 'CROSS';
	const NATURAL = 'NATURAL';
	const INNER = 'INNER';
	const OUTER = 'OUTER';
	const LEFT = 'LEFT';
	const RIGHT = 'RIGHT';
		
	/**
	 * @var array
	 */
	protected $keywords = array(
		'type' => null,
		'table' => null,
		'on' => null,
		//'using' => null,
	);
	
	/**
	 * @var SelectColumns
	 */
	protected $columns;
	
	/**
	 * "PARTITION (p1, p2)"
	 * @var Partition
	 */
	private $partition;
	
	/**
	 * `schema`.`table`
	 * @var string
	 */
	private $quotePrefixTableRef;

	/**
	 * @param string|array $table : 'table', ['table'] или ['alias' => 'table']
	 * @param string $schema
	 */
	public function __construct($table, $schema = null) {	
		$this->table($table, $schema);
		$this->keywords['type'] = self::INNER . ' ' . self::JOIN;
	}
	
	/**
	 * @var Table $tableRef
	 */
	public function setTableRef(Table $tableRef) {
		$this->quotePrefixTableRef = $tableRef->getQuotePrefix();
		if ($this->keywords['on']) $this->keywords['on']->setPrefixTableRef($this->quotePrefixTableRef);
		return $this;
	}
	
	/** 
	 * @param string|array $table : 'table', ['table'] или ['alias' => 'table']
	 * @param string $schema
	 * @return Join
	 */
	public function table($table, $schema = null){
		$from = $this->keywords['table'] = new Table($table, $schema);
		if ($this->partition) {
			$from->setPartition($this->partition);
		}
		if ($this->columns && $this->columns->hasPrefixColumns()) {		
			$this->columns->setQuotePrefix($from->getQuotePrefix());
		}
		return $this;
	}
	
	/**
	 * [self::NATURAL, self::LEFT, self:OUTER]
	 * @param array $type
	 * @return Join
	 */
	public function type(array $type) {
		
		$straight_join = false;
		$keywords[3] = self::JOIN;
		
		foreach ($type as $string) {		
			switch (strtoupper($string)) {
				case self::NATURAL : 
					$keywords[0] = self::NATURAL;
					break;
				case self::LEFT : 			
					$keywords[1] = self::LEFT;
					break;
				case self::RIGHT :
					$keywords[1] = self::RIGHT;
					break;
				case self::INNER :
					$keywords[2] = self::INNER;
					break;	
				case self::OUTER :
					$keywords[2] = self::OUTER;
					break;
				case self::JOIN :
				case self::CROSS : 	
					break;
				case self::STRAIGHT_JOIN :
					$straight_join = true;
					break;
				default :
					throw new Exception\InvalidArgumentException('Invalid type Join');
			}	
		}
		if ($straight_join) {
			$this->keywords['type'] = self::STRAIGHT_JOIN;
		}
		else {
			$this->keywords['type'] = '';
			for ($i = 0; $i < 3; $i++) {
				if (isset($keywords[$i])) $this->keywords['type'] .= $keywords[$i] . ' ';
			}
			$this->keywords['type'] = $this->keywords['type'] . self::JOIN;
		}
		return $this;
	}
	
	/**
	 * "PARTITION (p1, p2)"
	 * @param array $parts
	 * @return Join
	 */
	public function partition(array $parts) {
		if ($this->keywords['table']) {
			$this->keywords['table']->setPartition(new Partition($parts));
		} 
		else {
			$this->partition = new Keyword\Partition($parts);
		}
        return $this;	
	}
	
	/**
     * @param  array $columns :
     *  ['*'] или [Keyword\SelectColumns::SQL_STAR]
     *  ['col1', 'col2', ...]
     *  ['col1', 'alias2' => 'col2', ...]
     * @param  bool $hasPrefixColumns
     * @return Join
     */
    public function columns(array $columns, $hasPrefixColumns = true) {
        $this->columns = new Keyword\SelectColumns($columns, $hasPrefixColumns);		
		if ($this->keywords['table'] && $hasPrefixColumns) {
			$this->columns->setQuotePrefix($this->keywords['table']->getQuotePrefix());
		}
		return $this;
    }
	
	/**
	 * ['columnTableRef'=>'columnTableJoin'] без префиксов!!!: ['id' => 'id']
	 * 'table1.id = table2.id and table1.age < table2.age'
	 * @param string | array | Expression $conditionalExpr
	 * @return Join
	 */
	public function on($conditionalExpr) {	
		$this->keywords['on'] = new On($conditionalExpr);
		$this->keywords['on']->setPrefixTableRef($this->quotePrefixTableRef);
		$this->keywords['on']->setPrefixTableJoin($this->keywords['table']->getQuotePrefix());
		return $this;
	}
	
	/**
	 * @param array $columns
	 * @return Join
	 */
	public function using(array $columns) {
		
		return $this;
	}
	
	/**
	 * @param AdapterInterface $adapter
	 */
	public function getSqlString(AdapterInterface $adapter = null) {	
		$sqlString = PHP_EOL;
		foreach ($this->keywords as $keyword) {
			if ( $keyword instanceof SqlInterface ) {
				$sqlString .= $keyword->getSqlString($adapter). ' ';
			}
			elseif ($keyword) {
				$sqlString .= $keyword . ' ';
			}
		}
		return $sqlString;		
	}
	
}
