<?php

namespace App\Http\Models;

use System\Database\ORM\Model;

class Category extends Model {

    protected $table = "categories";
    protected $fillable = ['name'];
    protected $casts = [];

    public function posts(){
        return $this->hasMany('\App\Http\Models\Post','cat_id', 'id');
    }
}