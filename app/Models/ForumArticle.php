<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumArticle extends Model
{
    use HasFactory;

    protected $fillable = ['forum_feed_id', 'title', 'forum_content', 'user_id','media'];

    public function feed()
    {
        return $this->belongsTo(ForumFeed::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function uploadMedia($file)
    {
        $userId = auth()->id();
        $timestamp = now()->format('Ymd\THis');
        $extension = $file->getClientOriginalExtension();
        $filename = "forum_{$userId}_{$timestamp}.{$extension}";

        return $file->storeAs('forum_media', $filename, 'public');
    }




    public function comments()
    {
        return $this->hasMany(ForumComment::class,'forum_article_id');
    }

}
