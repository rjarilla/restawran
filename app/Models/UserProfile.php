<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    // Table name
    protected $table = 'UserProfile';

    // Primary key
    protected $primaryKey = 'UserProfileID';
    public $incrementing = false;
    protected $keyType = 'string';

    // Timestamps
    public $timestamps = false;

    // Mass assignable attributes
    protected $fillable = [
        'UserProfileID',
        'UserProfileName',
        'UserProfileUpdateDate',
        'UserProfileUpdateBy',
    ];
}
