<?php

namespace System\Config;

class Config{

    private static $instance;
    private $config_nested_array = [];
    private $config_dot_array = [];

    private function __construct(){

        $this->initialConfigArrays();
    }

    private function initialConfigArrays(){

        $configPath = dirname(__DIR__, 2) . '/config';
        foreach (glob($configPath . '*.php') as $fileName){
            $config = require $fileName;
            $key = $fileName;
            $key = str_replace($configPath, '', $key);
            $key = str_replace('.php', '', $key);
            $this->config_nested_array[$key] = $config;
        }

        $this->initialDefaultValues();
        $this->config_dot_array = $this->arrayDot($this->config_nested_array);
    }

    private static function getInstance(){

        if(empty(self::$instance)){

            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function get($key){

        $instance = self::getInstance();
        if(isset($instance->config_dot_array[$key])){
            return $instance->config_dot_array[$key];
        }
        else{
            throw new \Exception('"' . $key . '" not exist in config array');
        }
    }
}