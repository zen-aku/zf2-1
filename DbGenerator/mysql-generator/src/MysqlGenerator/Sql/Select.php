<?php

namespace MysqlGenerator\Sql;

use MysqlGenerator\Sql\Keyword;

class Select extends AbstractDml {
		
	const SELECT = 'SELECT';
	
	/**
	 * @var array
	 */
	protected $keywords = array(
		'select'	=> self::SELECT,
		'columns'	=> null,
		'joinColumns'=> null,
		'from'		=> null,
		'join'		=> null,
		
	);	
	
	/**
	 * "PARTITION (p1, p2)"
	 * @var Keyword\Partition
	 */
	private $partition;
		
	/**
     * @param string|array $table : 'table', ['table'] или ['alias' => 'table']
	 * @param string $schema
     */
    public function __construct($table = null, $schema = null){
		if ($table) $this->from($table, $schema);
    }

	/** 
	 * @param string|array $table : 'table', ['table'] или ['alias' => 'table']
	 * @param string $schema
	 * @return Select
	 */
	public function from($table, $schema = null){
		$from = $this->keywords['from'] = new Keyword\From($table, $schema);
		if ($this->partition) {
			$from->setPartition($this->partition);
		}
		if ($columns = $this->keywords['columns'] && $columns->hasPrefixColumns()) {		
			$columns->setQuotePrefix($from->getQuotePrefix());
		}
		if ($joinContainer = $this->keywords['join']) {
			foreach ($joinCntainer as $join) {
				$join->setTableRef($this->keywords['from']);
			}
		}
		return $this;
	}
	
	/**
	 * "PARTITION (p1, p2)"
	 * @param array $parts
	 * @return Select
	 */
	public function partition(array $parts) {
		if ($this->keywords['from']) {
			$this->keywords['from']->setPartition(new Keyword\Partition($parts));
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
     * @return Select
     */
    public function columns(array $columns, $hasPrefixColumns = true) {
        $col = $this->keywords['columns'] = new Keyword\SelectColumns($columns, $hasPrefixColumns);		
		if ($this->keywords['from'] && $hasPrefixColumns) {
			$col->setQuotePrefix($this->keywords['from']->getQuotePrefix());
		}
		return $this;
    }
	
	/**
	 * @param Keyword\Join $join
	 */
	public function join( $join ) {
		if (!$this->keywords['join']) $this->keywords['join'] = new Keyword\JoinContainer();
		
		if ($join instanceof Keyword\Join) {
			$join->setTableRef($this->keywords['from']);
			$this->keywords['join']->addJoin($join);
			
			$str = $join->getQuoteColumnsJoin();
			if ($str && $this->keywords['columns']) {
				$this->keywords['joinColumns'] .= ', ' . $str;	
			}
		}
		return $this;
	}
	
		
}
