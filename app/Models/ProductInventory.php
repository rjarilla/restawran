<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    protected $table = 'productinventory';
    protected $primaryKey = 'ProductBatchID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'ProductBatchID',
        'ProductID',
        'ProductQuantity',
        'ProductQuantityRemaining',
        'ProductBatchDeliveryDate',
        'ProductBatchExpiry',
        'ProductReceivedBy',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID', 'ProductID');
    }
}
