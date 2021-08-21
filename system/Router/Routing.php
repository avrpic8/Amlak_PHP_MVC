<?php

namespace System\Router;

use ReflectionMethod;

class Routing{

    private array $current_route;
    private string $method_field;
    private array $routes;
    private array $values = [];

    public function __construct(){

        $this->current_route = explode("/", CURRENT_ROUTE);
        $this->method_field = $this->methodField();

        global $routes;
        $this->routes = $routes;
    }

    private function methodField(): string{

        $method_field = strtolower($_SERVER['REQUEST_METHOD']);
        if($method_field == 'post'){
            if(isset($_POST['_method'])){
                if($_POST['_method'] == 'put'){
                    $method_field = 'put';
                }
                elseif ($_POST['_method'] == 'delete'){
                    $method_field = 'delete';
                }
            }
        }
        return $method_field;
    }

    public function run(){

        $match = $this->match();
        if(empty($match)){
            $this->error404();
        }

        $classPath = str_replace('\\', '/', $match['class']);
        $path = BASE_DIR . "/app/Http/Controllers/" . $classPath . ".php";
        if(!file_exists($path)){
            $this->error404();
        }

        $class = "\App\Http\Controllers\\" . $match["class"];
        $object = new $class();
        if(method_exists($object, $match['method'])){
            try {
                $reflection = new ReflectionMethod($class, $match['method']);
                $parameterCount = $reflection->getNumberOfParameters();
                if($parameterCount <= count($this->values)){
                    call_user_func_array(array($object, $match['method']), $this->values);
                }
                else{
                    $this->error404();
                }
            } catch (\ReflectionException $e) {}
        }
        else{
            $this->error404();
        }
    }

    public function match(): array{

        $reservedRoutes = $this->routes[$this->method_field];
        foreach ($reservedRoutes as $reservedRoute){
            if($this->compare($reservedRoute['url']) == true){
                return [
                    "class" => $reservedRoute['class'],
                    "method" => $reservedRoute['method']
                ];
            }
            else{
                $this->values = [];
            }
        }
        return [];
    }

    private function compare($reservedRouteUrl): bool{

        // part 1
        if(trim($reservedRouteUrl, '/') === ''){
            return trim($this->current_route[0], '/') === '';
        }
        // part 2
        $reservedRouteUrlArray = explode('/', $reservedRouteUrl);
        if(sizeof($this->current_route) != sizeof($reservedRouteUrlArray)){
            return false;
        }
        // part 3
        foreach ($this->current_route as $key => $currentRoutElement){
            $reservedRouteUrlElement = $reservedRouteUrlArray[$key];
            if(substr($reservedRouteUrlElement, 0, 1) == "{" and substr($reservedRouteUrlElement, -1) == "}"){
                array_push($this->values, $currentRoutElement);
            }
            elseif ($reservedRouteUrlElement != $currentRoutElement){
                return false;
            }
        }
        return true;
    }

    public function error404(){

        http_response_code(404);
        return include __DIR__ . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . '404.php';
        //exit();
    }
}