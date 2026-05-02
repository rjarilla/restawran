<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'rt_users';

    protected $primaryKey = 'UserID';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = true;

    protected $fillable = [
        'UserID',
        'UserName',
        'UserPassword',
        'UserProfileID',
        'UserStatus',
    ];

    /**
     * OPTIONAL: Hide password when returning model
     */
    protected $hidden = [
        'UserPassword',
    ];

    /*
    |------------------------------------------------
    | RELATIONSHIPS (ONLY KEEP IF TABLES EXIST)
    |------------------------------------------------
    */

    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class, 'UserProfileID', 'UserProfileID');
    }

    public function userProfilePrivileges()
    {
        return $this->hasMany(UserProfPrivileges::class, 'UserProfileID', 'UserProfileID');
    }
}