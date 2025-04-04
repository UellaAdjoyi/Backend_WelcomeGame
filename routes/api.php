<?php

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

//Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:3,1');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);
Route::post('/generate-reset-code', [AuthController::class, 'generateResetCode']);
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);
Route::post('/forgot-password', [AuthController::class, 'sendResetLink']);
Route::post('/reset-password', [AuthController::class, 'reset']);

//Profile
Route::middleware('auth:sanctum')->get('/profile', [ProfileController::class, 'getProfile']);
Route::middleware('auth:sanctum')->put('/updateProfile', [ProfileController::class, 'updateProfile']);

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
Route::get('/tasks', [TaskController::class, 'index']);
Route::middleware('auth:sanctum')->put('/tasks/{taskId}/progress', [TaskController::class, 'updateProgress']);
Route::middleware('auth:sanctum')->get('/tasks/completed', [TaskController::class, 'getCompletedTasks']);


//dashboard
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'getUsers']);
Route::post('/user/{id}/role', [UserController::class, 'updateRole']);
Route::get('/stats', [UserController::class, 'getStats']);
