<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    //
    use HasFactory;
    protected $collection = 'purchases';
    protected $fillable = [
        'purchase_person',
        'amount',
        'created_at',
        'payment_mode',
    ];
}
