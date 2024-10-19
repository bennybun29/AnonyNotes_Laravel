<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\HeartsController;

// Authentication routes for registering and logging in users
Route::post('register', [UsersController::class, 'register']); // Route for user registration
Route::post('login', [UsersController::class, 'login']); // Route for user login

//Routes for users CRUD
Route::get('users', [UsersController::class, 'index']);// Route for retrieving all users
Route::get('users/{id}', [UsersController::class, 'show']);// Route for retrieving a specific user
Route::delete('users/{id}', [UsersController::class, 'destroy']);// Route for deleting a specific user
Route::put('users/{id}', [UsersController::class, 'update']);// Route for updating a specific user
Route::get('/users/{id}/bio', [UsersController::class, 'getUserBio']);// Route for retrieving a specific user's bio



//routes for notes CRUD
Route::post('notes', [UsersController::class, 'store']); // Route for creating a new note
Route::get('notes', [NotesController::class, 'index']);// Route for retrieving all notes
Route::get('notes/{id}', [NotesController::class, 'show']);// Route for retrieving a specific note
Route::delete('notes/{id}', [NotesController::class, 'destroy']);// Route for deleting a specific note
Route::put('notes/{id}', [NotesController::class, 'update']);// Route for updating a specific note
Route::get('/users/{id}/notes', [UsersController::class, 'getUserNotes']);// Route for retrieving all notes for a specific user

//routes for comments CRUD
Route::get('comments', [CommentsController::class, 'index']);// Route for retrieving all comments
Route::get('comments/{id}', [CommentsController::class, 'show']);// Route for retrieving a specific comment
Route::delete('comments/{id}', [CommentsController::class, 'destroy']);// Route for deleting a specific comment
Route::put('comments/{id}', [CommentsController::class, 'update']);// Route for updating a specific comment
Route::get('/users/{id}/comments', [UsersController::class, 'getUserComments']);// Route for retrieving all comments for a specific user

//routes for hearts CRUD
Route::get('hearts', [HeartsController::class, 'index']);// Route for retrieving all hearts
Route::get('hearts/{id}', [HeartsController::class, 'show']);// Route for retrieving a specific heart
Route::delete('hearts/{id}', [HeartsController::class, 'destroy']);// Route for deleting a specific heart
Route::put('hearts/{id}', [HeartsController::class, 'update']);// Route for updating a specific heart
Route::get('/users/{id}/hearts', [UsersController::class, 'getUserHearts']);// Route for retrieving all hearts for a specific user

//Route for Change Password
Route::post('change-password', [UsersController::class, 'changePassword'])->middleware('auth:sanctum');







// Protect the following routes using Sanctum middleware (requires authentication)
Route::middleware('auth:sanctum')->group(function () {

    // Routes for User CRUD operations
    Route::resource('users', UsersController::class)->except(['register', 'login']);

    // Routes for Notes CRUD operations
    Route::resource('notes', NotesController::class);

    // Routes for Comments CRUD operations
    Route::resource('comments', CommentsController::class);

    // Routes for Hearts (likes) CRUD operations
    Route::resource('hearts', HeartsController::class);

    // Route for logging out the authenticated user
    Route::post('logout', [UsersController::class, 'logout']);
});
