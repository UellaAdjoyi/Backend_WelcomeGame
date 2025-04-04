<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\UserTaskProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index()
    {
        return response()->json(Task::all());
    }

    public function store(Request $request)
    {
        $task = Task::create($request->all());
        return response()->json($task, 201);
    }


    public function show($id)
    {
        $task = Task::find($id);
        if ($task) {
            return response()->json($task);
        }

        return response()->json(['error' => 'Tâche non trouvée'], 404);
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        if ($task) {
            $task->delete();
            return response()->json(['message' => 'Tâche supprimée']);
        }

        return response()->json(['error' => 'Tâche non trouvée'], 404);
    }

    // Controller pour mettre à jour la progression d'une tâche pour un utilisateur

    public function updateProgress(Request $request, $taskId)
    {
        // Vérifie si l'utilisateur est authentifié
        $user = auth()->user();

        // Trouve la tâche
        $task = Task::find($taskId);
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        // Vérifie si l'utilisateur a déjà une progression pour cette tâche
        $progress = $user->tasks()->where('task_id', $taskId)->first();

        if (!$progress) {
            // Si l'utilisateur n'a pas encore de progression, crée un nouvel enregistrement
            $progress = new UserTaskProgress();
            $progress->user_id = $user->id;
            $progress->task_id = $taskId;
        }

        // Met à jour la progression
        $progress->completed = $request->completed;  // 'completed' doit être envoyé dans la requête
        $progress->save();

        return response()->json(['status' => 'updated']);
    }


    public function getCompletedTasks(Request $request)
    {
        // Vérifiez si l'utilisateur est authentifié
        $user = auth()->user();

        // Si l'utilisateur n'est pas authentifié
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated user'], 401);
        }

        // Récupérer les tâches complétées de l'utilisateur
        $completedTasks = Task::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('completed', true);
        })->get();

        // Vérifiez si des tâches ont été trouvées
        if ($completedTasks->isEmpty()) {
            return response()->json(['error' => 'No completed task'], 404);
        }

        // Retourner les tâches complétées en réponse JSON
        return response()->json($completedTasks);
    }
}
