<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    protected $fillable = [];

    public function participations()
    {
        return $this->hasMany(Participation::class);
    }
}
