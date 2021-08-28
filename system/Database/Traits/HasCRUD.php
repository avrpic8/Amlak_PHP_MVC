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
}