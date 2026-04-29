<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'rt_users';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'UserName',
        'Password',
        'Role',
    ];

    /**
     * OPTIONAL: Hide password when returning model
     */
    protected $hidden = [
        'Password',
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