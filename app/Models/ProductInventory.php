<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    protected $table = 'ProductInventory';
    protected $primaryKey = 'ProductBatchID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'ProductBatchID',
        'ProductID',
        'ProductQuantity',
        'ProductBatchDeliveryDate',
        'ProductBatchExpiry',
        'ProductReceivedBy',
    ];
}