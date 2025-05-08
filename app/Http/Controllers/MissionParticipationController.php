<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\Participation;
use Illuminate\Http\Request;

class MissionParticipationController extends Controller
{
    public function submitParticipation(Request $request)
    {
        $request->validate([
            'mission_id' => 'required|exists:outdoor_missions,id',
            'photo' => 'required|image',
        ]);

        $photoPath = $request->file('photo')->store('missions', 'public');
        $mission = Mission::find($request->mission_id);

        $isLate = $mission->deadline && now()->greaterThan($mission->deadline);

        return Participation::create([
            'user_id' => auth()->id(),
            'mission_id' => $mission->id,
            'photo_proof_url' => $photoPath,
            'status' => $isLate ? 'late' : 'pending',
            'submitted_at' => now(),
        ]);
    }

    public function validateParticipation($id, Request $request)
    {
        $participation = Participation::findOrFail($id);
        $participation->status = $request->status; // 'accepted' ou 'rejected'
        $participation->validated_at = now();
        $participation->save();

        if ($request->status == 'accepted') {
            $user = $participation->user;
            $user->increment('points', $participation->mission->points);
        }

        return response()->json(['success' => true]);
    }
}
