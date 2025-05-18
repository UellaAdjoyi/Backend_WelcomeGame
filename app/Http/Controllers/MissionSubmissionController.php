<?php

namespace App\Http\Controllers;

use App\Models\MissionSubmission;
use Illuminate\Http\Request;

class MissionSubmissionController extends Controller
{
    public function submit(Request $request) {
        $request->validate(['mission_id' => 'required|exists:missions,id', 'photo' => 'required|image']);
        $path = $request->file('photo')->store('submissions', 'public');

        return MissionSubmission::create([
            'user_id' => auth()->id(),
            'mission_id' => $request->mission_id,
            'photo' => $path
        ]);
    }

    public function pending() {
        return MissionSubmission::with('user', 'mission')->where('status', 'pending')->get();
    }

    public function validateSubmission(Request $request, $id) {
        $submission = MissionSubmission::findOrFail($id);
        $request->validate(['status' => 'required|in:accepted,rejected', 'points' => 'nullable|integer']);
        $submission->update([
            'status' => $request->status,
            'points_awarded' => $request->status === 'accepted' ? $request->points : null
        ]);
        return response()->json(['message' => 'Updated']);
    }
    public function mySubmissions()
    {
        return MissionSubmission::with('mission')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

}
