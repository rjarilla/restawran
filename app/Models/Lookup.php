<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lookup extends Model
{
    protected $table = 'Lookup';
    protected $primaryKey = 'LookupID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'LookupID',
        'LookupCategory',
        'LookupName',
        'LookupValue',
        'LookupUpdateDate',
        'LookupUpdateBy',
    ];
}