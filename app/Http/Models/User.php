<?php

namespace App\Http\Models;

use System\Database\ORM\Model;

class User extends Model {

    protected $table = "users";
    protected $fillable = ['username'];
    protected $casts = [];

    public function roles(){
        return $this->belongsToMany
        (
            '\App\Http\Models\Role',
            'user_role',
            'id',
            'user_id',
            'role_id',
            'id'
        );
    }
}