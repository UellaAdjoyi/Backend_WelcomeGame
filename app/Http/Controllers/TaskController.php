<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\UserTaskProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
   /* public function index()
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
    }*/

   /* public function index() {
        return Task::where('is_active', true)->get();
    }*/

    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $tasks = Task::where('is_active', true)->get();

        // Tâches complétées par l'utilisateur connecté
        $completedTaskIds = $user->completedTasks()
            ->where('completed', true)
            ->pluck('task_id')
            ->toArray();

        $tasks->transform(function ($task) use ($completedTaskIds) {
            $task->completed = in_array($task->id, $completedTaskIds);
            return $task;
        });

        return response()->json($tasks);
    }



    public function getCompletedTasks(Request $request) {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $completedTasks = Task::whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id)->where('completed', 1);
        })->get();

        return response()->json($completedTasks);
    }

    public function store(Request $request) {
        return Task::create($request->all());
    }

    public function update(Request $request, $id) {
        $task = Task::findOrFail($id);
        $task->update($request->all());
        return $task;
    }

    public function toggleComplete($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $task = Task::findOrFail($id);

        $existingPivot = $user->completedTasks()->where('task_id', $task->id)->first();

        if ($existingPivot) {
            $newStatus = !$existingPivot->pivot->completed;
            $user->completedTasks()->updateExistingPivot($task->id, ['completed' => $newStatus]);
        } else {
            // Si la tâche n'est pas encore associée à l'utilisateur, on l'ajoute avec completed = true
            $user->completedTasks()->attach($task->id, ['completed' => true]);
        }

        return response()->json(['message' => 'Task completion toggled']);
    }

    public function taskStats()
    {
        $tasks = Task::withCount('completedByUsers')->get();
        $totalUsers = User::count();

        $data = $tasks->map(function ($task) use ($totalUsers) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'completed_count' => $task->completed_by_users_count,
                'completion_rate' => $totalUsers > 0 ? round(($task->completed_by_users_count / $totalUsers) * 100, 2) : 0,
            ];
        });

        return response()->json($data);
    }

}
