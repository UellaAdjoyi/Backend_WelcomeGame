<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        try {
            // Vérifie si l'utilisateur est authentifié
            $user = Auth::user();

            Log::info('Authenticated user: ', ['user' => $user]);

            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }
            // $role = $user->is_admin == 1 ? 'admin' : 'user';

            // Retourne les informations de l'utilisateur
            return response()->json([

                'role' => $user->role,
                'email' => $user->email,
                'username' => $user->username
            ]);
        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            Log::error('Error fetching profile: ' . $e->getMessage());
            return response()->json(['error' => 'Could not fetch profile data.'], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email_address' => 'required|email|max:255|unique:users,email_address,' . $user->id,
            'phone_number' => 'nullable|string|max:15',
        ]);

        $user->update($validatedData);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }
}
