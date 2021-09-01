<?php

namespace System\Database\Traits;

use System\Database\DBConnection\DBConnection;

trait HasCRUD{

    protected function fill(): string{

        $fillArray = array();
        // `user`.`email` = ?;
        foreach ($this->fillable as $attribute){
            if(isset($this->$attribute)){
                array_push($fillArray, $this->getAttributeName($attribute) . " = ?");
                $this->inCastsAttributes($attribute) == true ? $this->addValue($attribute, $this->castEncodeValue
                ($attribute, $this->$attribute)) : $this->addValue($attribute, $this->$attribute);
            }
        }
        return implode(', ', $fillArray);
    }

    protected function findMethod($id){

        $this->setSql("SELECT * FROM ".$this->getTableName());
        $this->setWhere("AND", $this->getAttributeName($this->primaryKey)." = ?");
        $this->addValue($this->primaryKey, $id);
        $stmt = $this->executeQuery();
        $data = $stmt->fetch();
        $this->setAllowedMethods(['update', 'delete', 'find']);
        if($data){
            return $this->arrayToAttributes($data);
        }
        return null;
    }

    protected function saveMethod(){

        $fillString = $this->fill();
        if(!isset($this->{$this->primaryKey})){
            $this->setSql("INSERT INTO ".$this->getTableName()." SET $fillString, ".$this->getAttributeName
                ($this->createdAt)."=NOW()");
        }else{
            $this->setSql("UPDATE ".$this->getTableName()." SET $fillString, ".$this->getAttributeName
                ($this->updatedAt)."=NOW()");
            $this->setWhere("AND", $this->getAttributeName($this->primaryKey)." = ?");
            $this->addValue($this->primaryKey, $this->{$this->primaryKey});
        }

        $this->executeQuery();
        $this->resetQuery();

        if(!isset($this->{$this->primaryKey})){
            $object = $this->findMethod(DBConnection::newInsertId());
            $defaultVars = get_class_vars(get_called_class());
            $allVars = get_object_vars($object);
            $diffVars = array_diff(array_keys($allVars), array_keys($defaultVars));
            foreach ($diffVars as $attribute){
                $this->inCastsAttributes($attribute) == true ? $this->registerAttribute($this, $attribute,
                    $this->castEncodeValue($attribute, $object->$attribute)) :
                    $this->registerAttribute($this, $attribute, $object->$attribute);
            }
        }

        $this->resetQuery();
        $this->setAllowedMethods(['update', 'delete', 'find']);
        return $this;
    }

    protected function createMethod($values){

        $values = $this->arrayToCastEncodeValue($values);
        $this->arrayToAttributes($values, $this);
        return $this->saveMethod();
    }

    protected function updateMethod($values){

        $values = $this->arrayToCastEncodeValue($values);
        $this->arrayToAttributes($values, $this);
        return $this->saveMethod();
    }

    protected function deleteMethod($id = null){

        $object = $this;
        $this->resetQuery();
        if($id){
            $object = $this->findMethod($id);
            $this->resetQuery();
        }
        $object->setSql("DELETE FROM ". $object->getTableName());
        $object->setWhere("AND", $this->getAttributeName($this->primaryKey)." = ?");
        $object->addValue($object->primaryKey, $object->{$object->primaryKey});

        return $object->executeQuery();
    }

    protected function allMethod(): array{

        $this->setSql("SELECT * FROM ".$this->getTableName());
        $stmt = $this->executeQuery();
        $data = $stmt->fetchAll();

        if($data){
            $this->arrayToObject($data);
            return $this->collection;
        }

        return [];
    }

    protected function whereMethod($attribute, $firstValue, $secondValue = null){

        if($secondValue === null){
            $condition = $this->getAttributeName($attribute). ' = ?';
            // `users.id`. = ?
            $this->addValue($attribute, $firstValue);
        } else{
            $condition = $this->getAttributeName($attribute). ' '.$firstValue.' ?';
            // `users.id`. <= ?
            $this->addValue($attribute, $secondValue);
        }
        $operator = 'AND';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods
        (
            ['where', 'whereOr', 'find',
            'whereIn', 'whereNull', 'whereNotNull',
            'limit', 'orderBy', 'get',
            'paginate'
            ]
        );
        return $this;
    }

    protected function whereOrMethod($attribute, $firstValue, $secondValue = null){

        if($secondValue === null){
            $condition = $this->getAttributeName($attribute). ' = ?';
            // `users.id`. = ?
            $this->addValue($attribute, $firstValue);
        } else{
            $condition = $this->getAttributeName($attribute). ' '.$firstValue.' ?';
            // `users.id`. <= ?
            $this->addValue($attribute, $secondValue);
        }
        $operator = 'OR';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods
        (
            ['where', 'whereOr', 'find',
                'whereIn', 'whereNull', 'whereNotNull',
                'limit', 'orderBy', 'get',
                'paginate'
            ]
        );
        return $this;
    }

    protected function whereNullMethod($attribute){

        $condition = $this->getAttributeName($attribute).' IS NULL';
        $operator = 'AND';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods
        (
            ['where', 'whereOr', 'find',
                'whereIn', 'whereNull', 'whereNotNull',
                'limit', 'orderBy', 'get',
                'paginate'
            ]
        );
        return $this;
    }

    protected function whereNotNullMethod($attribute){

        $condition = $this->getAttributeName($attribute).' IS NOT NULL';
        $operator = 'AND';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods
        (
            ['where', 'whereOr', 'find',
                'whereIn', 'whereNull', 'whereNotNull',
                'limit', 'orderBy', 'get',
                'paginate'
            ]
        );
        return $this;
    }

    protected function whereInMethod($attribute, $values){

        if(is_array($values)){
            $valuesArray = [];
            foreach ($values as $value){
                $this->addValue($attribute, $value);
                array_push($valuesArray, '?');
            }
            $operator = 'AND';
            $condition = $this->getAttributeName($attribute).' IN ('.implode(' , ', $valuesArray).')';
            $this->setWhere($operator, $condition);

            $this->setAllowedMethods
            (
                ['where', 'whereOr', 'find',
                    'whereIn', 'whereNull', 'whereNotNull',
                    'limit', 'orderBy', 'get',
                    'paginate'
                ]
            );
            return $this;
        }
    }

    protected function orderByMethod($attribute, $expression){

        $this->setOrderBy($attribute, $expression);
        $this->setAllowedMethods(['limit', 'orderBy', 'get', 'paginate']);
        return $this;
    }

    protected function limitMethod($from, $number){

        $this->setLimit($from, $number);
        $this->setAllowedMethods(['limit', 'get', 'paginate']);
        return $this;
    }

    protected function getMethod($array = []){

        if($this->sql == ''){
            if(empty($array)){
                $fields = $this->getTableName().'.*';
            }
            else{
                foreach ($array as $key => $field){
                    $array[$key] = $this->getAttributeName($field);
                }
                $fields = implode(' , ', $array);
            }
            $this->setSql("SELECT $fields FROM ".$this->getTableName());
        }

        $stmt = $this->executeQuery();
        $data = $stmt->fetchAll();

        if($data){
            $this->arrayToObject($data);
            return $this->collection;
        }
        return [];
    }

    protected function paginateMethod($perPage){

        $totalRows = $this->getCount();
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $totalPages = ceil($totalRows / $perPage);
        $currentPage = min($currentPage, $totalPages);
        $currentPage = max($currentPage, 1);
        $currentRow = ($currentPage - 1) * $perPage;
        $this->setLimit($currentRow, $perPage);

        if($this->sql == ''){
            $this->setSql("SELECT ".$this->getTableName().".* FROM ".$this->getTableName());
        }

        $stmt = $this->executeQuery();
        $data = $stmt->fetchAll();

        if($data){
            $this->arrayToObject($data);
            return $this->collection;
        }
        return [];
    }
}