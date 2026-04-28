<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'rt_users';

    protected $fillable = [
        'UserName',
        'Password',
        'Role',
    ];

    public $timestamps = true;
}