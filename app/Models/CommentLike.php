<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    protected $fillable = ['forum_comment_id', 'user_id'];

    public function comment()
    {
        return $this->belongsTo(ForumComment::class, 'forum_comment_id');
}

}
