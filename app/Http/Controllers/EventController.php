<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\UserPoint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function getNearbyEvents(Request $request)
    {

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1',
        ]);

        Log::info('Latitude:', ['latitude' => $request->query('latitude')]);
        Log::info('Longitude:', ['longitude' => $request->query('longitude')]);
        Log::info('Radius:', ['radius' => $request->query('radius')]);

        $latitude = $request->query('latitude');
        $longitude = $request->query('longitude');
        $radius = $request->query('radius', 100);

        try {
            $events = Event::select('*')
                ->selectRaw(
                    "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
                    [$latitude, $longitude, $latitude]
                )
                ->having('distance', '<=', $radius / 1000) // Convertir en kilomètres
                ->orderBy('distance')
                ->get();
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des événements: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la récupération des événements.'], 500);
        }

        // Retourner la réponse
        if ($events->isEmpty()) {
            return response()->json(['message' => 'Aucun événement trouvé à proximité'], 404);
        }

        return response()->json($events);
    }

    public function createEvent(Request $request)
    {
        try {
            $radius = $request->input('radius', 100);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after_or_equal:start_time',
                'radius' => 'nullable|numeric|min:1'
            ]);
            if (Event::where([
                'name' => $request->input('name'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'start_time' => $request->input('start_time'),
            ])->exists()) {
                return response()->json([
                    'error' => 'An event with these details already exists.'
                ], 422);
            }
            $event = Event::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'radius' => $radius,
            ]);

            return response()->json([
                'message' => 'Event create successfully.',
                'event' => $event,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Invalid data.',
                'details' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error occured',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function joinEvent(Request $request, $eventId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'User not authenticated.'], 401);
            }

            $event = Event::find($eventId);
            if (!$event) {
                return response()->json(['message' => 'Event not found.'], 404);
            }

            $pointsAwarded = 10;
            UserPoint::create([
                'user_id' => $user->id,
                'points' => $pointsAwarded,
            ]);
            return response()->json(['message' => 'You have join the event and gain points.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server error.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getUserPoints(Request $request)
    {
        $user = $request->user();
        $points = $user->user_points()->sum('points');
        return response()->json(['user_points' => $points]);
    }

    public function index()
    {
        $events = Event::all([
            'id',
            'name',
            'description',
            'latitude',
            'longitude',
            'start_time',
            'end_time',
            'radius',
        ]);
        return response()->json($events);
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        return response()->json($event);
    }

    public function updateEvent(Request $request, $id)
    {
        try {
            $event = Event::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after_or_equal:start_time',
                'radius' => 'nullable|numeric|min:1',
            ]);

            $event->update($validatedData);

            return response()->json(['message' => 'Event updated successfully.', 'event' => $event], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating event.', 'details' => $e->getMessage()], 500);
        }
    }

    public function deleteEvent($id)
    {
        try {
            $event = Event::findOrFail($id);

            $event->delete();

            return response()->json(['message' => 'Event deleted successfully.'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting event: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting event.'], 500);
        }
    }
}
