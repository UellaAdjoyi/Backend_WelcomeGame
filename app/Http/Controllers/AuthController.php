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
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{


    public function login(Request $request)
    {
        try {
            $request->validate([
                'login' => 'required|string|exists:users,login',
                'password' => 'required',
            ]);

            $key = 'login-attempts:' . $request->ip();

            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json(['message' => 'Too many login attempts. Please try again later.'], 429);
            }

            $user = User::where('login', $request->login)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return ['message' => 'The provided credentials are incorrect.'];
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            RateLimiter::clear($key);

            if ($user->must_change_password) {
                return response()->json([
                    'message' => 'Please change your password.',
                    'force_password_change' => true,
                    'token' => $token,
                    'user' => $user
                ], 200);
            }

            return [
                'user' => $user,
                'token' => $token,
            ];
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'current password incorrect .'], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->must_change_password = false;
        $user->save();

        return response()->json(['message' => 'Password successfully changed.']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Password reset link sent.'])
            : response()->json(['error' => 'Unable to send reset link.'], 500);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->must_change_password = false;
        $user->save();

        return response()->json(['message' => 'Password has been successfully updated.']);
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

}
