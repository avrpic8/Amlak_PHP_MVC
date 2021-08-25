<?php

namespace System\Database\Traits;

trait HasAttributes{

    private function registerAttribute($object, string $attribute, $value){
        // $user->name
        $this->inCastsAttributes($attribute) == true ?
            $object->$attribute = $this->castDecodeValue($attribute, $value) :
            $object->$attribute = $value;
    }

    protected function arrayToAttribute(){

    }

    protected function arrayToObject(){

    }

    private function inHiddenAttributes(){

    }

    private function inCastsAttributes(){

    }

    private function castDecodeValue(){

    }

    private function castEncodeValue(){

    }

    private function arrayToCastEncodeValue(){

    }
}