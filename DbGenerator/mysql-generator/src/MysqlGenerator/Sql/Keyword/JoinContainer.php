<?php
namespace MysqlGenerator\Sql\Keyword;

use MysqlGenerator\Adapter\AdapterInterface;

class JoinContainer extends AbstractKeyword implements \IteratorAggregate {
	
	/**
	 * @var array Join 
	 */
	protected $joinContainer = array();
	
	/** 
	 * @return \ArrayIterator
	 */
	public function getIterator() {       
        return new \ArrayIterator($this->joinContainer);  
    }
	
	/**
	 * @param Join
	 * @return JoinContainer
	 */
	public function addJoin(Join $join) {
		$this->joinContainer[] = $join;
		return $this;
	}
	
	/**
	 * @param AdapterInterface $adapter
	 * @return string
	 */
	public function getSqlString(AdapterInterface $adapter = null) {
		$str = '';
		foreach ($this->joinContainer as $join) {
			$str .= $join->getSqlString($adapter);
		}
		return $str;
	}
	
}
