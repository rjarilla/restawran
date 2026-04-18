<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    protected $table = 'OrderDetails';
    protected $primaryKey = 'OrderDetailsID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'OrderDetailsID',
        'OrderID',
        'ProductID',
        'OrderQuantity',
        'OrderQuantityPrice',
        'OrderItemTotal',
    ];
}