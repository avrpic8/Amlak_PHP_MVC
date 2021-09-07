<?php

namespace System\View\Traits;

use Exception;

trait HasViewLoader{

    private $viewNameArray = [];

    /**
     * @throws Exception
     */
    private function viewLoader($dir){

        $dir = trim($dir, " .");
        $dir = str_replace(".", "/", $dir);             // check dir path if has error
        if(file_exists(dirname(__DIR__, 3)."/resources/view/$dir.blade.php")){
            $this->registerView($dir);
            return htmlentities(file_get_contents(dirname(__DIR__, 3)."/resources/view/$dir.blade.php"));
        }else{
            throw new Exception('view not found!');
        }
    }

    private function registerView($view){

        array_push($this->viewNameArray, $view);
    }

}