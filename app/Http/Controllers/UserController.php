<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\Event;
use App\Models\ForumFeed;
use App\Models\Task;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Friend;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{

    //Récupération des utilisateurs et de leur rôle sur le dashboard
    public function getUsers()
    {
        $user = Auth::user(); // Utilisateur actuel

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $users = User::select('id', 'email',  'role')->get();

        return response()->json($users);
    }
    public function getAllUsers()
    {
        $user = Auth::user(); // Utilisateur actuel

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Récupérer les IDs des amis
        $friendIds = $user->friends()->pluck('friend_id')->toArray();

        // Ajouter l'utilisateur actuel dans les amis pour s'assurer qu'il est exclu de la liste des utilisateurs
        $friendIds[] = $user->id;

        // Récupérer tous les utilisateurs qui ne sont pas amis avec l'utilisateur actuel
        $users = User::whereNotIn('id', $friendIds)->get();

        return response()->json($users);
    }


    public function addFriend(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $friendId = $request->friend_id;

        // Vérifier si l'amitié existe déjà
        if ($user->friends()->where('friend_id', $friendId)->exists()) {
            return response()->json(['message' => 'Already friends'], 400);
        }

        // Ajouter les deux utilisateurs comme amis (relation bidirectionnelle)
        $user->friends()->create(['friend_id' => $friendId]);
        User::find($friendId)->friends()->create(['friend_id' => $user->id]);

        return response()->json(['message' => 'Friend added successfully']);
    }



    public function getFriends()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $friends = $user->friends()->with('friend')->get();

        return response()->json($friends);
    }



    public function getFriendIds()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Récupérer les IDs des amis
        $friendIds = $user->friends()->pluck('friend_id')->toArray();

        return response()->json($friendIds);
    }

    public function getFriendsWithDetails()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Récupérer les amis avec leurs détails
        $friends = $user->friends()->with('friend')->get()->map(function ($friendRelation) {
            return $friendRelation->friend;
        });

        return response()->json($friends);
    }


    public function updateRole(Request $request, $id)
    {
        $user = User::find($id); // Vérification
        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable'], 404);
        }

        $validatedData = $request->validate([
            'role' => 'required|in:user,moderator,admin'
        ]);

        $user->role = $validatedData['role'];
        $user->save();

        return response()->json(['message' => 'Rôle mis à jour avec succès']);
    }

    public function getStats()
    {
        $totalUsers = User::count();
        $totalPosts = ForumFeed::count();
        $totalTasks = Task::count();

        return response()->json([
            'totalUsers' => $totalUsers,
            'totalPosts' => $totalPosts,
            'totalTasks' => $totalTasks,
        ]);
    }

    public function updateStatus($id, Request $request)
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $user = User::findOrFail($id);
        $user->is_active = $request->is_active;
        $user->save();

        return response()->json(['message' => 'Status updated.']);
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        $newPassword = Str::random(10);
        $user->password = Hash::make($newPassword);
        $user->must_change_password = true;
        $user->save();

        Mail::to($user->email)->send(new ResetPasswordMail($user, $newPassword));

        return response()->json(['message' => 'Password reset and email sent.']);
    }

    public function uploadProfilePicture(Request $request)
    {
        $user = auth()->user();

        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $request->validate([
            'file' => 'image|max:2048', // Max 2 Mo
        ]);

        $file = $request->file('file');

        try {
            // Appel de la méthode upload dans le modèle
            $user->profile_picture = $user->uploadProfile($file);
            $user->save();

            // On retourne seulement le chemin relatif, Angular ajoutera le host
            return response()->json([
                'profile_picture' => $user->profile_picture
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error uploading file: ' . $e->getMessage()], 500);
        }
    }
}
