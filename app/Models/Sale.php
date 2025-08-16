<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    // 
    use HasFactory;
    protected $fillable = [
        'cash_sale',
        'online_sale',
        'credit_sale',
        'total_sale',
        'overall_sale',
        'month',
        'created_at',
        'date'
    ];
}
