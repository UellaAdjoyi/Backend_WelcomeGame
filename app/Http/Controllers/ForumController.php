<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\ForumArticle;
use App\Models\ForumComment;
use App\Models\ForumFeed;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ForumController extends Controller
{
    // Create a forum feed
    public function createFeed(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

        $feed = ForumFeed::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json($feed, 201);
    }

    // Add  article
    public function addArticle(Request $request, $feedId)
    {
        $request->validate([
            'title' => 'required|string',
            'forum_content' => 'required|string',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mkv',
        ]);

        $feed = ForumFeed::findOrFail($feedId);
        $article = new ForumArticle([
            'title' => $request->title,
            'forum_content' => $request->forum_content,
            'user_id' => Auth::id(),
            'forum_feed_id' => $feedId,

        ]);

        // Upload  medias
        if ($request->hasFile('media')) {
            $article->media = $article->uploadMedia($request->file('media'));
        }
        $article->save();

        return response()->json($article, 201);
    }

    //delete article (moderator / admin)
    public function deleteFeed($id)
    {
        $feed = ForumFeed::findOrFail($id);

            $feed->delete();
            return response()->json(null, 204);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $feed = ForumFeed::findOrFail($id);
        $feed->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Forum feed updated.', 'feed' => $feed]);
    }


    public function getAllFeeds()
    {
        $feeds = ForumFeed::with('articles')->get();
        return response()->json($feeds, 200);
    }

    public function show($id)
    {
        // Get feed id
        $forumFeed = ForumFeed::find($id);

        if (!$forumFeed) {
            return response()->json(['message' => 'Forum feed not found'], 404);
        }

        return response()->json($forumFeed);
    }
    public function getArticles($id)
    {
        $feed = ForumFeed::with('articles.comments.user','articles.user')->findOrFail($id);
        return response()->json($feed);
    }


    public function addComment($articleId, Request $request)
    {
        $request->validate([
            'article_content' => 'required|string|max:1000',
        ]);

        $article = ForumArticle::findOrFail($articleId);

        $comment = ForumComment::create([
            'forum_article_id' => $article->id,
            'user_id' => Auth::id(),
            'article_content' => $request->article_content,
        ]);

        return response()->json($comment, 201);
    }

    public function getComments($articleId)
    {
        $userId = auth()->id();

        $comments = ForumComment::where('forum_article_id', $articleId)
            ->with(['user', 'likes']) // charge aussi les likes
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($comment) use ($userId) {
                return [
                    'id' => $comment->id,
                    'article_content' => $comment->article_content,
                    'user' => $comment->user,
                    'likes' => $comment->likes->count(),
                    'likedByUser' => $comment->likes->contains('user_id', $userId),
                    'created_at' => $comment->created_at,
                ];
            });

        return response()->json($comments);
    }

    public function updateArticle(Request $request, $id)
    {
        $article = ForumArticle::findOrFail($id);
        $user = auth()->user();

        if ($article->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $article->update($request->only(['title', 'forum_content']));
        return response()->json(['message' => 'Article updated']);
    }

    public function deleteArticle($id)
    {
        $article = ForumArticle::findOrFail($id);
        $user = auth()->user();

        if ($article->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $article->delete();
        return response()->json(['message' => 'Article deleted']);
    }


}
