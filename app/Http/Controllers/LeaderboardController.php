<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index()
    {
        $leaderboard = DB::table('users')
            ->join('mission_submissions', 'users.id', '=', 'mission_submissions.user_id')
            ->where('mission_submissions.status', 'accepted')
            ->select(
                'users.id',
                'users.email',
                DB::raw('SUM(COALESCE(mission_submissions.points_awarded, 0)) as total_points')
            )
            ->groupBy('users.id', 'users.email')
            ->orderByDesc('total_points')
            ->get();

        return response()->json($leaderboard);

    }
}
