<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommonEvent extends Model
{
    //
    use HasFactory;
    protected $collection = 'common_events'; // Specify the collection name
    protected $fillable = [
        'events',
    ];
}
