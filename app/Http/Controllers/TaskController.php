<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // Récupérer toutes les tches de l'utilisateur connecté
    public function index()
    {
        $tasks = Task::where('user_id', Auth::id())->get();
        return response()->json($tasks);
    }

    // Créer une nouvelle tâche
    public function store(Request $request)
    {
        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validation des données de la tâche
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'guide_url' => 'nullable|url',
            'registration_url' => 'nullable|url',
            'pieces' => 'nullable|array',
        ]);

        // Ajouter le user_id à la création de la tâche
        $task = Task::create(array_merge($validated, ['user_id' => Auth::id()]));

        return response()->json($task, 201);
    }


    // Mettre à jour une tâche
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // Vérification d'autorisation
        if ($task->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'completed' => 'required|boolean',
        ]);

        $task->completed = $validated['completed'];
        $task->save();

        return response()->json($task);
    }

    // Supprimer une tâche
    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }

    // Récupérer les tâches complétées
    public function CompletedTasks()
    {
        $tasks = Task::where('user_id', Auth::id())
            ->where('completed', true)
            ->get();
        return response()->json($tasks);
    }

    // Mettre à jour le statut d'une tâche
    public function updateTaskStatus(Request $request, $id)
    {
        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Récupérer la tâche par son ID
        $task = Task::findOrFail($id);

        // Vérifier si la tâche appartient à l'utilisateur authentifié
        if ($task->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validation de la demande de mise à jour
        $validated = $request->validate([
            'completed' => 'required|boolean',
        ]);

        // Mise à jour de l'état de la tâche
        $task->completed = $validated['completed'];
        $task->save();

        return response()->json($task);
    }
}
