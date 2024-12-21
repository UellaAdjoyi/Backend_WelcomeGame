<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'guide_url',
        'registration_url',
        'pieces',
        'completed',
        'user_id',
    ];

    protected $casts = [
        'pieces' => 'array',
        'completed' => 'boolean',
    ];
}
