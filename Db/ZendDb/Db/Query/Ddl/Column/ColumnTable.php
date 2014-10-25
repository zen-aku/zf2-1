<?php

namespace Zend\Db\Query\Ddl\Column;

use Zend\Db\Query\Ddl\DdlColumnInterface;

/**
 * 
 */
class ColumnTable {
    
    /**
     * @var DdlColumnInterface
     */
    protected $ddlCommand;
     
    /**
     * @param DdlColumnInterface $ddlCommand
     */
    public function __construct(DdlColumnInterface $ddlCommand) {
        $this->ddlCommand = $ddlCommand;
    }
   
    /** 
     * Добавляет в DdlColumnInterface::addColumn() объект Integer  и возвращает объект Integer для дальнейшего его редактирования
     * @param null|string $name
     * @param bool $nullable
     * @param null|string|int $default
     * @param array $options
     * @return Column\Integer
     */
    public function integer($name = null, $nullable = false, $default = null, array $options = []) {
        return $this->addColumn( new Integer($name, $nullable, $default, $options) );
    }
    
    /**
     * Добавляет в DdlColumnInterface::addColumn() объект Varchar  и возвращает объект Varchar для дальнейшего его редактирования
     * @param type $name
     * @param type $length
     * @return \Zend\Db\Query\Ddl\Column\Varchar
     */
    public function varchar($name = null, $length = null) {
        return $this->addColumn( new Varchar($name, $length) );
    }
    
    
    /**
     * Добавляет в DdlColumnInterface::addColumn() объект ColumnInterface  и возвращает объект ColumnInterface для дальнейшего его редактирования
     * @param ColumnInterface $column
     * @return ColumnInterface
     */
    protected function addColumn(ColumnInterface $column) {
        $this->ddlCommand->addColumn($column);
        return $column;
    }


    /*
    public function float($name, $digits, $decimal) {
        return new Float($name, $digits, $decimal);
    }
    
    public function decimal($name, $precision, $scale = null) {
        return new Decimal($name, $precision, $scale);
    }
    */
    
}

