<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

//Auth-Users
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:3,1');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);
Route::post('/generate-reset-code', [AuthController::class, 'generateResetCode']);
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);
Route::middleware('auth:sanctum')->post('/change-password', [AuthController::class, 'changePassword']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);


//Users
Route::middleware('auth:sanctum')->get('/profile', [ProfileController::class, 'getProfile']);

Route::middleware('auth:sanctum')->post('/user/upload-profile-picture', [UserController::class, 'uploadProfilePicture']);


//Events
Route::post('events', [EventController::class, 'createEvent']);
Route::get('/nearby-events', [EventController::class, 'getNearbyEvents']);
Route::put('/events/{id}', [EventController::class, 'updateEvent']);
Route::delete('/events/{id}', [EventController::class, 'deleteEvent']);
Route::middleware('auth:sanctum')->post('/events/{eventId}/join', [EventController::class, 'joinEvent']);
Route::middleware('auth:sanctum')->get('/points', [EventController::class, 'getUserPoints']);
Route::middleware('auth:sanctum')->get('/admin/events', [EventController::class, 'index']);


//Leaderboard
Route::get('/leaderboard', [LeaderboardController::class, 'index']);

//Friends
Route::middleware('auth:sanctum')->get('/users', [UserController::class, 'getAllUsers']);
Route::middleware('auth:sanctum')->post('/add-friend', [UserController::class, 'addFriend']);
Route::middleware('auth:sanctum')->get('/friends', [UserController::class, 'getFriends']);
Route::post('/send-invitation', [UserController::class, 'sendInvitation']);
Route::post('/friends/accept', [UserController::class, 'acceptInvitation']);
Route::get('/friends/ids', [UserController::class, 'getFriendIds']);


//Forum
Route::get('/posts', [ForumController::class, 'index']);
Route::post('/posts', [ForumController::class, 'store'])->middleware('auth:sanctum');;
Route::get('/posts/{id}', [ForumController::class, 'show']);
Route::get('/posts/{id}/comments', [ForumController::class, 'showComment']);
Route::middleware('auth:sanctum')->post('/create-posts', [ForumController::class, 'createPosts']);
Route::put('/update-posts/{id}', [ForumController::class, 'updatePosts']);
Route::delete('/delete-posts/{id}', [ForumController::class, 'deletePosts']);
Route::delete('/comments/{id}', [ForumController::class, 'destroy']);
Route::middleware('auth:sanctum')->post('/posts/{id}/comments', [ForumController::class, 'addComment']);

//Tasks
Route::middleware('auth:sanctum')->get('/tasks', [TaskController::class, 'index']);


Route::middleware('auth:sanctum')->post('/tasks/{task}/toggle-complete', [TaskController::class, 'toggleComplete']);
Route::middleware('auth:sanctum')->get('/user/completed-tasks', [TaskController::class, 'getCompletedTasks']);



//ADMIN
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'getUsers']);
Route::post('/user/{id}/role', [UserController::class, 'updateRole']);
/*Route::put('/user/users/{id}/status', [UserController::class, 'updateStatus']);*/
Route::middleware('auth:sanctum')->put('/user/users/{id}/status', [UserController::class, 'updateStatus']);
Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword']);
Route::get('/stats', [UserController::class, 'getStats']);
Route::post('/users', [AdminController::class, 'createUser']);
Route::middleware('auth:sanctum')->post('/tasks/create', [TaskController::class, 'store']);
Route::middleware('auth:sanctum')->get('/admin/task-stats', [TaskController::class, 'taskStats']);



//Missions
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/missions', [MissionController::class, 'index']);
    Route::post('/missions/{mission}/participer', [ParticipationController::class, 'store']);
    Route::get('/admin/participations', [ParticipationController::class, 'index']);
    Route::post('/admin/participations/{id}/valider', [ParticipationController::class, 'valider']);
});
