<?php

namespace System\Database\Traits;
use System\Database\DBConnection\DBConnection;

trait HasQueryBuilder{

    private string $sql = '';
    protected $where    = [];
    private $orderBy    = [];
    private $limit      = [];
    private $values     = [];
    private $bindValues = [];

    protected function getSql(): string{
        return $this->sql;
    }

    protected function setSql(string $query): void{
        $this->sql = $query;
    }

    protected function resetSql(){
        $this->sql = '';
    }

    protected function setWhere($operator, $condition): void{
        $array = ['operator' => $operator, 'condition' => $condition];
        array_push($this->where, $array);
    }

    protected function resetWhere(){
        $this->where = [];
    }

    protected function setOrderBy($name, $expression){
        array_push($this->orderBy, $name . ' ' . $expression);
    }

    protected function resetOrderBy(){
        $this->orderBy = [];
    }

    protected function setLimit($from, $number){
        $this->limit['from'] = (int) $from;
        $this->limit['number'] = (int) $number;
    }

    protected function resetLimit(){
        unset($this->limit['from']);
        unset($this->limit['number']);
    }

    protected function addValue($attribute, $value){
        $this->values[$attribute] = $value;
        array_push($this->bindValues, $value);
    }

    protected function removeValues(){
        $this->values = [];
        $this->bindValues = [];
    }

    protected function resetQuery(){
        $this->resetSql();
        $this->resetWhere();
        $this->resetOrderBy();
        $this->resetLimit();
        $this->removeValues();
    }
}