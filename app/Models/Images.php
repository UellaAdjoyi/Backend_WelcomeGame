<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    protected $fillable = ['post_id', 'file_path'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
