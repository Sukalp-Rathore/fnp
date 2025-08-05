<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bouquet extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'total_items',
        'items',
        'total_price',
        'receipt_exist',
        'delivery_date',
        'delivery_address',
        'bouquet_image',
        'making_charge',
        'created_by',
    ];
}
 