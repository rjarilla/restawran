<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Customer extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'CustomerID';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'CustomerID',
        'CustomerCode',
        'CustomerName',
        'CustomerEmail',
        'CustomerContactNumber',
        'CustomerAddressLine1',
        'CustomerAddressLine2',
        'CustomerStreet',
        'CustomerCity',
        'CustomerProvince',
        'CustomerPostalCode',
        'CustomerUpdateBy',
        'CustomerUpdateDate',
    ];

    public function orders()
    {
        return $this->hasMany(Orders::class, 'CustomerID', 'CustomerID');
    }

    public function getTable()
    {
        return Schema::hasTable('customer') ? 'customer' : parent::getTable();
    }
}
