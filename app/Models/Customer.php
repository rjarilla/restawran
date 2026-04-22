<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'Customer';
    protected $primaryKey = 'CustomerID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'CustomerID',
        'CustomerName',
        'CustomerAddressLine1',
        'CustomerAddressLine2',
        'CustomerStreet',
        'CustomerCity',
        'CustomerProvince',
        'CustomerPostalCode',
        'CustomerEmail',
        'CustomerContactNumber',
        'CustomerUpdateBy',
        'CustomerUpdateDate',
    ];

    public function orders()
    {
        return $this->hasMany(Orders::class, 'CustomerID', 'CustomerID');
    }
}
