<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Users;
use App\Models\UserProfile;

class UserProfPrivileges extends Model
{
    protected $table = 'userprofprivileges';
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

    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class, 'UserProfileID', 'UserProfileID');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(Users::class, 'UserProfPrivilegesUpdateBy', 'UserID');
    }
}
