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
        'ProductDiscountStartDate', 'ProductDiscountEndDate', 'ProductUpdatedBy', 'ProductUpdatedDate'
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class, 'ProductID', 'ProductID');
    }

    public function inventories()
    {
        return $this->hasMany(ProductInventory::class, 'ProductID', 'ProductID');
    }
}
?>

