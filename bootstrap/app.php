<?php

// dd helper function

use App\Category;
use App\Post;

function dd($variable){

    echo "<pre>";
    print_r($variable);
    exit();
}

require_once ("../config/app.php");
require_once ("../config/database.php");

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


$post = Post::find(1);
$category = $post->category();
dd($category);

/// run routing system
$routing = new System\Router\Routing();
$routing->run();


