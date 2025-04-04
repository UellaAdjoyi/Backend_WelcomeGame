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

    ];

    protected $casts = [
        'pieces' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('completed', 'updated_at')->withTimestamps();
    }
    public function userProgress()
    {
        return $this->hasMany(UserTaskProgress::class);
    }
}
