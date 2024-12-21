<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'content',
        'user_id',
        'post_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class); // Un commentaire appartient à un utilisateur
    }

    public function post()
    {
        return $this->belongsTo(Post::class); // Un commentaire appartient à un post
    }
}
