<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';
    protected $primaryKey = 'ProductID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ProductID', 'ProductName', 'ProductDescription', 'ProductCategoryID',
        'ProductQuantityTypeID', 'ProductImagePath', 'ProductPrice', 
        'ProductOnDiscount', 'ProductPriceSale', 'ProductStatus', 
        'ProductUpdatedBy', 'ProductUpdatedDate'
    ];
}
?>