<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfPrivileges extends Model
{
    protected $table = 'UserProfPrivileges';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $keyType = 'string';
    protected $fillable = [
        'UserProfileID',
        'UserPrivilegesID',
        'UserProfPrivilegesUpdateDate',
        'UserProfPrivilegesUpdateBy',
    ];
}