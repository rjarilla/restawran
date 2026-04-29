<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'PaymentID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'PaymentID',
        'OrderID',
        'PaymentMode',
        'PaymentTotal',
        'PaymentChange',
    ];

    public function order()
    {
        return $this->belongsTo(Orders::class, 'OrderID', 'OrderID');
    }
}