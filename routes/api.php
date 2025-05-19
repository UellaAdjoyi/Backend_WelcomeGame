<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\CommentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\MissionSubmissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
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
Route::middleware('auth:sanctum')->get('/profile', [UserController::class, 'getProfile']);
Route::middleware('auth:sanctum')->get('/points', [UserController::class, 'getUserPoints']);
Route::middleware('auth:sanctum')->post('user/upload-profile-picture', [UserController::class, 'uploadProfilePicture']);


//Events
Route::post('events', [EventController::class, 'createEvent']);
Route::get('/nearby-events', [EventController::class, 'getNearbyEvents']);
Route::put('/events/{id}', [EventController::class, 'updateEvent']);
Route::delete('/events/{id}', [EventController::class, 'deleteEvent']);
Route::middleware('auth:sanctum')->post('/events/{eventId}/join', [EventController::class, 'joinEvent']);
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
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/forum/feeds-create', [ForumController::class, 'createFeed']);
    Route::post('/forum/feeds/{feedId}/articles', [ForumController::class, 'addArticle']);
    Route::delete('/forum/feed/{feedId}', [ForumController::class, 'deleteFeed']);
    Route::delete('/forum-articles/{id}', [ForumController::class, 'deleteArticle']);
    Route::get('/forum/feeds', [ForumController::class, 'getAllFeeds']);
    Route::get('/forum-feeds/{id}', [ForumController::class, 'show']);
    Route::get('/forum-feeds/{id}/articles', [ForumController::class, 'getArticles']);

});
Route::middleware('auth:sanctum')->put('/forum-feeds/{id}', [ForumController::class, 'update']);
Route::middleware('auth:sanctum')->put('/forum-articles/{id}', [ForumController::class, 'updateArticle']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/articles/{articleId}/comments', [ForumController::class, 'addComment']);
    Route::get('/articles/{articleId}/comments', [ForumController::class, 'getComments']);

});
Route::middleware('auth:sanctum')->post('/forum-comments/{id}/like', [CommentController::class, 'likeComment']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/comments/{comment}', [CommentController::class, 'updateComment']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});

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
Route::delete('/deleteUsers/{id}', [AdminController::class, 'destroy']);



//Missions
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/missions', [MissionController::class, 'index']);
    Route::post('/missions', [MissionController::class, 'store']);

    Route::post('/missions/submit', [MissionSubmissionController::class, 'submit']); // user
    Route::get('/missions/pending', [MissionSubmissionController::class, 'pending']); // admin/modo
    Route::put('/missions/validate/{id}', [MissionSubmissionController::class, 'validateSubmission']); // admin/modo
});

Route::middleware('auth:sanctum')->get('/my-submissions', [MissionSubmissionController::class, 'mySubmissions']);
Route::get('/leaderboard', [LeaderboardController::class, 'index']);
