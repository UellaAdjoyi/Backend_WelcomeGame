<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $table = 'events';

    protected $fillable = [
        'name',
        'description',
        'latitude',
        'longitude',
        'start_time',
        'end_time',
        'radius',
    ];
}
