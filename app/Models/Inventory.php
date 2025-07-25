<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'product_name',
        'type',
        'cost_price',
        'selling_price',
        'product_image',
        'quantity',
        'total_stock_amount',
        'color',
    ];
}
