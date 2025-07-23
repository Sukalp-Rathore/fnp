<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseVendor extends Model
{
    //
    use HasFactory;
    protected $collection = 'purchase_vendors';
    protected $fillable = [
        'name',
        'total_purchase',
        'amount_pending'
    ];
}
