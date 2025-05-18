<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MissionSubmission extends Model
{
    protected $fillable = ['user_id', 'mission_id', 'photo', 'status', 'points_awarded'];
    public function mission() {
        return $this->belongsTo(Mission::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
}
