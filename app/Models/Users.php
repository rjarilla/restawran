<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'rt_users';
    protected $primaryKey = 'UserID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'UserID',
        'UserName',
        'UserPassword',
        'UserProfileID',
        'UserStatus',
        'UserUpdateDate',
        'UserUpdateBy',
    ];

    // Relationship: Users belongs to UserProfile
    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class, 'UserProfileID', 'UserProfileID');
    }

    // Relationship: Users has many privileges through UserProfPrivileges
    public function userProfilePrivileges()
    {
        return $this->hasMany(UserProfPrivileges::class, 'UserProfileID', 'UserProfileID');
    }
}