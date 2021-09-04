<?php

namespace App\Http\Models;

use System\Database\ORM\Model;

class Post extends Model {

    protected $table = "posts";
    protected $fillable = ['title', 'body', 'cat_id'];
    protected $casts = [];

    public function category(){
        return $this->belongsTo('\App\Http\Models\Category', 'cat_id', 'id');
    }
}