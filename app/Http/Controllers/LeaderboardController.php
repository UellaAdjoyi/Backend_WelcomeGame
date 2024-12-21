<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index()
    {
        $users = User::withSum('points', 'points')
            ->orderByDesc('points_sum_points')
            ->take(5)
            ->get(['id', 'username']);

        $leaderboard = $users->map(function ($user) {
            return [
                'username' => $user->username,
                'total_points' => $user->points_sum_points ?? 0,
            ];
        });

        return response()->json($leaderboard);
    }
}
