<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers'; // your correct table

    protected $primaryKey = 'CustomerID';

    public $timestamps = false;

    protected $fillable = [
        'CustomerCode',
        'CustomerName',
        'CustomerEmail',
        'CustomerContactNumber',
        'CustomerAddressLine1',
        'CustomerCity',
        'CustomerProvince',
        'CustomerUpdateDate',
    ];
}