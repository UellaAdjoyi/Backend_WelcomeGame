<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Friend;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    //Récupération des utilisateurs et de leur rôle sur le dashboard
    public function getUsers()
    {
        $user = Auth::user(); // Utilisateur actuel

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $users = User::select('id', 'last_name', 'first_name', 'role')->get();

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

    //Changement de role d'un utilisateur
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
        $totalPosts = Post::count();
        $totalEvents = Event::count();

        return response()->json([
            'totalUsers' => $totalUsers,
            'totalPosts' => $totalPosts,
            'totalEvents' => $totalEvents,
        ]);
    }
}
