<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ForumController extends Controller
{
    public function index()
    {
        $posts = Post::all(['id', 'title', 'created_at']);
        return response()->json($posts);
    }

    public function show($id)
    {
        $post = Post::with(['user', 'comments.user'])->findOrFail($id);
        if ($post->image) {
            $post->image_url = asset('storage/' . $post->image);
        }

        return response()->json([
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
            'author' => $post->user->username,
            'created_at' => $post->created_at->format('Y-m-d H:i:s'),
            'comments' => $post->comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'author' => $comment->user->username,
                    'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }


    public function showComment($id)
    {
        $post = Post::with(['user', 'comments.user'])->findOrFail($id);

        return response()->json([
            'id' => $post->id,
            'title' => $post->title,
            'content' => $post->content,
            'author' => $post->user->username,
            'created_at' => $post->created_at->format('Y-m-d H:i:s'),
            'comments' => $post->comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'author' => $comment->user->username,
                    'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }
    public function createPosts(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Validation d'image
        ]);

        $post = Post::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'user_id' => Auth::id(),
        ]);

        // Si une image a été envoyée, on l'enregistre dans la table 'images'
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public'); // Sauvegarde dans le dossier public/images
            $post->images()->create([
                'file_path' => $path,
            ]);
        }

        return response()->json($post, 201);
    }



    public function addComment(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);
        $post = Post::findOrFail($postId);

        $comment = Comment::create([
            'content' => $request->input('content'),
            'user_id' => Auth::id(),
            'post_id' => $postId,
        ]);

        return response()->json($comment, 201);
    }

    public function updatePosts(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string|max:255',

            ]);
            $post = Post::find($id);

            if (!$post) {
                return response()->json(['error' => 'Post not found'], 404);
            }
            $post->title = $validatedData['title'];
            $post->content = $validatedData['content'];
            $post->save();

            return response()->json([
                'message' => 'Post updated successfully.',
                'post' => $post,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return response()->json(['error' => 'Could not update profile.'], 500);
        }
    }

    public function deletePosts($id)
    {
        try {
            $post = Post::findOrFail($id);

            $post->delete();

            return response()->json(['message' => 'Post deleted successfully.'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting event: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting post.'], 500);
        }
    }

    public function destroy($id)
    {
        $comment = Comment::find($id);

        if ($comment) {
            $comment->delete();
            return response()->json(['message' => 'Comment deleted'], 200);
        }

        return response()->json(['message' => 'Comment  not found'], 404);
    }
}
