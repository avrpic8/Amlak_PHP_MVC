<?php

namespace System\Request\Traits;

trait HasRunValidation{

    protected function errorRedirect(){

        if($this->errorExist == false){
            return $this->request;
        }

        return back();
    }

    protected function checkFirstError($name){

        if(!errorExist($name) and !in_array($name, $this->errorVariablesName)){
            return true;
        }

        return false;
    }

    protected function checkFieldExist($name){

        return (isset($this->request[$name]) && !empty($this->request[$name])) ? true : false;
    }

    protected function checkFileExist($name){

        if(isset($this->files[$name]['name'])){
            if(!empty($this->files[$name]['name'])){
                return true;
            }
        }

        return false;
    }

    protected function setError($name, $errorMessage){

        array_push($this->errorVariablesName, $name);
        error($name, $errorMessage);
        $this->errorExist = true;
    }
}