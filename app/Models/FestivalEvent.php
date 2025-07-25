<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FestivalEvent extends Model
{
    //
    use HasFactory;
    protected $collection = 'festival_events'; // Specify the collection name
    protected $fillable = [
        'events',
        'event_date',
    ];
}
