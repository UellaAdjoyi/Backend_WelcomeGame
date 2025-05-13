<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CommentLike;
use App\Models\ForumComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $articleId)
    {
        $request->validate([
            'article_content' => 'required|string',
        ]);

        $comment = new ForumComment([
            'forum_article_id' => $articleId,
            'user_id' => Auth::id(),
            'content' => $request->article_content,
        ]);

        $comment->save();

        return response()->json($comment, 201);
    }

    public function likeComment($id)
    {
        $userId = auth()->id();

        $existing = CommentLike::where('forum_comment_id', $id)->where('user_id', $userId)->first();
        if ($existing) {
            $existing->delete(); // toggle : unlike
        } else {
            CommentLike::create([
                'forum_comment_id' => $id,
                'user_id' => $userId,
            ]);
        }

        return response()->json(['likes' => ForumComment::find($id)->likes()->count()]);
    }

}
