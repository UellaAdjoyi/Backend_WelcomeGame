<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;


class AdminController extends Controller
{
    public function resetPassword($userId)
    {
        // Vérifie si l'utilisateur est admin
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($userId);
        $newPassword = Str::random(10);
        $user->password = Hash::make($newPassword);
        $user->save();

        // Envoi vers un outil externe ou par mail
        // Mail::to('external-tool@etu.univ.fr')->send(new \App\Mail\PasswordResetNotification($user->login, $newPassword));

        return response()->json(['message' => 'New password sent externally']);
    }

    public function createUser(Request $request)
    {
        // Validation des données
        $request->validate([
            'login' => 'required|unique:users',
            'role' => 'required|in:admin,student',
            'email' => 'required|unique:users',
        ]);

        // Génération du mot de passe temporaire
        $password = Str::random(10); // Mot de passe temporaire
        $user = User::create([
            'login' => $request->login,
            'role' => $request->role,
            'email'=>$request->email,
            'password' => Hash::make($password),
        ]);

        // Envoi de l'email avec les informations de connexion
        Mail::to($user->email)->send(new WelcomeMail($user->login, $password));

        return response()->json([
            'login' => $user->login,
            'password' => $password,
            'message' => 'User created'
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

}
