<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'link',
        'is_active'

    ];

    protected $casts = [
        'pieces' => 'array',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class, 'user_task')
            ->withPivot('completed')
            ->withTimestamps();
    }

    public function completedByUsers()
    {
        return $this->belongsToMany(User::class, 'user_task')
            ->withPivot('completed')
            ->wherePivot('completed', true);
    }

}
