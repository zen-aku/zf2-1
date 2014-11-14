<?php
namespace MysqlGenerator\Sql\Keyword;

use MysqlGenerator\Adapter\AdapterInterface;
use MysqlGenerator\Sql\Exception\InvalidArgumentException;

class On extends AbstractKeyword {
	
	const KEYWORD = 'ON';
	
	/**
	 * @var string `schema`.`table`.
	 */
	protected $prefixTableRef;
	
	/**
	 * @var string `schema`.`table`.
	 */
	protected $prefixTableJoin;
	
	/**
	 * ['columnTableRef'=>'columnTableJoin'] без префиксов!!!: ['id' => 'id']
	 * 'table1.id = table2.id and table1.age < table2.age'
	 * @var string | array | Expression $conditionalExpr
	 */
	protected $conditionalExpr;

	/**
	 * ['columnTableRef'=>'columnTableJoin'] без префиксов!!!: ['id' => 'id']
	 * 'table1.id = table2.id and table1.age < table2.age'
	 * @param string | array | Expression $conditionalExpr
	 */
	public function __construct($conditionalExpr) {	
		if( !is_string($conditionalExpr) && !is_array($conditionalExpr) ) {
			throw new InvalidArgumentException('Invalid argument Join::on() or On::__construct(). Argument must be string or array or Expression');	
		}
		elseif ( is_array($conditionalExpr) && (!is_string(key($conditionalExpr)) || count($conditionalExpr) !== 1) ) {
			throw new InvalidArgumentException('Invalid argument Join::on() or On::__construct(). Join::on() or On::__construct() expects argument as an array is a single element associative array');	
		}	
		$this->conditionalExpr = $conditionalExpr;
	}

	/**
	 * @param string $prefix
	 * @return On
	 */
	public function setPrefixTableRef($prefix){
		$this->prefixTableRef = $prefix;
		return $this;
	}

	/**
	 * @param string $prefix
	 * @return On
	 */
	public function setPrefixTableJoin($prefix){
		$this->prefixTableJoin = $prefix;
		return $this;
	}
	
	/**
	 * 
	 * @return On
	 * @throws InvalidArgumentException
	 */
	public function getSqlString(AdapterInterface $adapter = null) {		
		
		if ( is_array($this->conditionalExpr)) {			
			$columnTableRef = $this->quoteIdentifier(key($this->conditionalExpr));			
			$columnTableRef = $this->prefixTableRef ? $this->prefixTableRef . $columnTableRef : $columnTableRef;
			
			$columnTableJoin = $this->quoteIdentifier(current($this->conditionalExpr));
			$columnTableJoin = $this->prefixTableJoin ? $this->prefixTableJoin . $columnTableJoin : $columnTableJoin;    
			
			$stringOn =  $columnTableRef . ' = ' . $columnTableJoin;
		}		
		elseif ( is_string($this->conditionalExpr) ) {	
			$stringOn = $this->quoteIdentifierInFragment($this->conditionalExpr, ['=', 'AND', 'OR', '(', ')', 'BETWEEN', '<', '>']);
		}					
		return self::KEYWORD . ' ' . $stringOn;
	}
	
}
