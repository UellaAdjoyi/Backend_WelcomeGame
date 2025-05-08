<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserTask extends Pivot
{
    protected $table = 'user_task';

    protected $fillable = ['user_id',
        'task_id',
        'completed'
    ];
}
