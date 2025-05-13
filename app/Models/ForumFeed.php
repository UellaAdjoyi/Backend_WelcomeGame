<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumFeed extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];

    public function articles()
    {
        return $this->hasMany(ForumArticle::class);
    }
}
