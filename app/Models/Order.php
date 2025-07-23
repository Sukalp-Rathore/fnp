<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    //
    use HasFactory;
    protected $collection = 'orders'; // Specify the collection name
    protected $fillable = [
        "customer_name_primary",
        "customer_name_secondary",
        "customer_email_primary",
        "customer_email_secondary",
        "customer_mobile_primary",
        "customer_mobile_secondary",
        "customer_address",
        "customer_address_secondary",
        "city",
        "event_name",
        "event_date",
        "delivery_date",
        "order_status",
        "products",
        "vendor",
        "order_status",
        "order_type",
        "order_price",
        "created_by"
    ];
}
