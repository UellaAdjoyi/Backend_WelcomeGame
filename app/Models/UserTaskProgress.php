<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTaskProgress extends Model
{
    protected $table = 'task_user';

    protected $fillable = ['user_id', 'task_id', 'completed'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
