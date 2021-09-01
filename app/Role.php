<?php

namespace App\Http\Models;

use System\Database\ORM\Model;

class Role extends Model {

    protected $table = "roles";
    protected $fillable = ['name'];
    protected $casts = [];

    public function users(){
        return $this->belongsToMany
        (
            '\App\Models\User',
            'user_role',
            'id',
            'role_id',
            'user_id',
            'id'
        );
    }
}