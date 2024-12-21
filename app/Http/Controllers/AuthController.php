<?php

namespace App\Http\Controllers;

// use Illuminate\Foundation\Auth\User;

use App\Mail\ResetPasswordMail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $fields = $request->validate([
                'email_address' => 'required|email|unique:users,email_address',
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'username' => 'required|string|max:255|unique:users,username',
                'phone_number' => 'required|min:10|max:10',
                'password' => 'required|min:8',
            ]);

            $fields['password'] = Hash::make($fields['password']);
            $user = User::create($fields);

            $verificationToken = $user->createToken('email_verification')->plainTextToken;

            Mail::to($request->email_address)->send(new WelcomeMail($verificationToken));

            return response()->json([
                'message' => 'Account created successfully. Please verify your email address.',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function verifyEmail($token)
    {
        $token = DB::table('personal_access_tokens')->where('id', $token)->first();

        if ($token) {
            $user = User::find($token->tokenable_id); // Utilisez `tokenable_id` pour lier le token à l'utilisateur

            if ($user) {
                $user->email_verified_at = now();
                $user->save();

                return response()->json(['message' => 'Email verified successfully']);
            } else {
                return response()->json(['message' => 'User not found'], 404);
            }
        } else {
            return response()->json(['message' => 'Invalid token ID'], 400);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|exists:users,username',
                'password' => 'required',

            ]);

            $user = User::where('username', $request->username)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return [
                    'message' => 'The provied credentials are incorrect.'
                ];
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    public function logout(Request $request)
    {
        try {
            if ($request->user()) {
                $request->user()->tokens->each(function ($token) {
                    $token->delete();
                });

                return response()->json([
                    'message' => 'Logout succesfully.',
                ], 200);
            }

            return response()->json([
                'error' => 'Not Authenticated.',
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email_address' => 'required|email|exists:users,email_address',
        ]);

        $user = User::where('email_address', $request->email_address)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $token = Password::createToken($user);

        $resetLink = url('/reset-password?token=' . urlencode($token) . '&email=' . urlencode($request->email_address));

        Mail::to($request->email_address)->send(new \App\Mail\ResetPasswordMail($resetLink));

        return response()->json(['message' => 'Password reset link sent successfully.'], 200);
    }



    public function reset(Request $request)
    {
        // Valider les données
        $request->validate([
            'token' => 'required',
            'email_address' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Trouver l'entrée dans `password_resets`
        $reset = DB::table('password_resets')->where('email', $request->email_address)->first();

        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return response()->json(['message' => 'Invalid token or email.'], 400);
        }

        // Mettre à jour le mot de passe de l'utilisateur
        $user = User::where('email_address', $request->email_address)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Supprimer l'entrée dans `password_resets`
        DB::table('password_resets')->where('email', $request->email_address)->delete();

        return response()->json(['message' => 'Password has been reset.'], 200);
    }
}
