<?php

session_start();
if(isset($_SESSION['temporary_flash'])) unset($_SESSION['temporary_flash']);
if(isset($_SESSION['temporary_errorFlash'])) unset($_SESSION['temporary_errorFlash']);
if(isset($_SESSION['old'])) unset($_SESSION['temporary_old']);

if(isset($_SESSION['old'])){
    $_SESSION['temporary_old'] = $_SESSION['old'];
    unset($_SESSION['old']);
}

if(isset($_SESSION['flash'])){
    $_SESSION['temporary_flash'] = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

if(isset($_SESSION['errorFlash'])){
    $_SESSION['temporary_errorFlash'] = $_SESSION['errorFlash'];
    unset($_SESSION['errorFlash']);
}

$params = [];
$params = !isset($_GET) ? $params : array_merge($params, $_GET);
$params = !isset($_POST) ? $params : array_merge($params, $_POST);

$_SESSION['old'] = $params;
unset($params);


use App\Http\Models\Category;
use App\Http\Models\Post;


require_once ("../system/helpers/helper.php");

require_once ("../config/app.php");
require_once ("../config/database.php");

global $routes;
$routes = ['get' =>[], 'post' =>[], 'put' =>[], 'delete'=> []];

/// reserved routes
require_once ("../routes/web.php");
require_once ("../routes/api.php");

/*$categories = Category::all();
foreach ($categories as $category){
    echo $category->id .'--'. $category->name.'--' ;
}*/

/*$categories = Category::paginate(2);
foreach ($categories as $category){
    echo $category->id .'--'. $category->name.'--' ;
}*/


/*$post = \App\Http\Models\Post::find(10);
dd($post->title);*/


/*$categories = Category::where('id', '>', 1)->get();
foreach ($categories as $category){
    echo $category->id .'--'. $category->name.'--' ;
}*/


/*$posts = Category::find(1)->posts()->get();
foreach ($posts as $post) {
    echo $post->id . '--' . $post->title . '--';
}*/


//$post = Post::find(1);
//$category = $post->category();
//dd($category);

//dd(\System\Config\Config::get("app.CURRENT_ROUTE"));

/// run routing system
$routing = new System\Router\Routing();
$routing->run();


