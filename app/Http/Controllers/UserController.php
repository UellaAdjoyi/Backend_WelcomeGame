<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getAllUsers()
    {
        $users = User::where('id', '!=', Auth::id())->get();
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

        // Ajouter un ami
        $user->friends()->create(['friend_id' => $friendId]);

        return response()->json(['message' => 'Friend added successfully']);
    }


    public function getFriends()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Récupérez les amis de l'utilisateur
        $friends = $user->friends()->with('friend')->get();

        return response()->json($friends);
    }
}
