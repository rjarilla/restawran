<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'Payment';
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
}