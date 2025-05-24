<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Comment;
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

    public function updateComment(Request $request, ForumComment $comment)
    {
        $user = Auth::user();

        if ($comment->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'article_content' => 'required|string|max:1000',
        ]);

        $comment->article_content = $validated['article_content'];
        $comment->save();

        return response()->json(['message' => 'Comment updated successfully']);
    }
    public function destroyComment(ForumComment $comment)
    {
        $user = Auth::user();

        if ($comment->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
