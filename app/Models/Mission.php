<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    protected $fillable = ['title', 'description'];
    public function submissions() {
        return $this->hasMany(MissionSubmission::class);
    }
}
