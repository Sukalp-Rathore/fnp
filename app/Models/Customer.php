<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'customer_name',
        'customer_address',
        'customer_email',
        'event_name',
        'sender_name',
        'customer_phone',
        'customer_type',
        'vendor_email',
        'product',
        'product_value',
        'message',
        'secondary_customers',
        'forward_date',
        'created_by',
        'event_date',
    ];
}
