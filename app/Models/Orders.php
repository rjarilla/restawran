<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'OrderID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'OrderID',
        'OrderDate',
        'CustomerID',
        'OrderTotalAmount',
        'OrderFulfilledBy',
    ];
}