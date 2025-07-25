<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    //
    use HasFactory;
    protected $collection = 'vendors';
    protected $fillable = [
        'first_name',
        'email',
        'mobile',
        'alternate_mobile',
        'city',
        'gender'
    ];
}
