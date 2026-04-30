<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    protected $table = 'orderdetails';
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

    public function order()
    {
        return $this->belongsTo(Orders::class, 'OrderID', 'OrderID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID', 'ProductID');
    }
}

