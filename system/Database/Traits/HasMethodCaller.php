<?php

namespace System\Database\Traits;

trait HasMethodCaller{

    private $allMethods =
    [
        'create', 'update', 'delete',
        'find', 'all', 'save',
        'where', 'whereOr', 'whereIn',
        'whereNull', 'whereNotNull', 'limit',
        'orderBy', 'get', 'paginate'
    ];

    private $allowedMethods =
    [
            'create', 'update', 'delete',
            'find', 'all', 'save',
            'where', 'whereOr', 'whereIn',
            'whereNull', 'whereNotNull', 'limit',
            'orderBy', 'get', 'paginate'
    ];

    private function methodCaller($object, $method, $args){

        $suffix = 'Method';
        $methodName = $method . $suffix;

        if(in_array($method, $this->allowedMethods)){
            return call_user_func_array(array($object, $methodName), $args);
        }
        //return false;
    }

    protected function setAllowedMethods(array $array){

        $this->allowedMethods = $array;
    }

    public function __call($name, $arguments){

        return $this->methodCaller($this, $name, $arguments);
    }

    public static function __callStatic($name, $arguments){

        $className = get_called_class();
        $instance = new $className;
        return $instance->methodCaller($instance, $name, $arguments);
    }
}