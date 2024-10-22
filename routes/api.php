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

// Protect the following routes using Sanctum middleware (requires authentication)
Route::middleware('auth:sanctum')->group(function () {

    //Routes for users CRUD
Route::get('users', [UsersController::class, 'index']);// Route for retrieving all users
Route::get('users/{id}', [UsersController::class, 'show']);// Route for retrieving a specific user
Route::delete('users/{id}', [UsersController::class, 'destroy']);// Route for deleting a specific user
Route::put('users/{id}', [UsersController::class, 'update']);// Route for updating a specific user
Route::get('/users/{id}/bio', [UsersController::class, 'getUserBio']);// Route for retrieving a specific user's bio
Route::get('/users/{id}/notes', [UsersController::class, 'getUserNotes']);// Route for retrieving all notes for a specific user

//routes for notes CRUD
Route::post('notes', [UsersController::class, 'store']); // Route for creating a new note
Route::get('notes', [NotesController::class, 'index']);// Route for retrieving all notes
Route::get('notes/{id}', [NotesController::class, 'show']);// Route for retrieving a specific note
Route::delete('notes/{id}', [NotesController::class, 'destroy']);// Route for deleting a specific note
Route::put('notes/{id}', [NotesController::class, 'update']);// Route for updating a specific note
Route::get('/notes/{note_id}/comments', [NotesController::class, 'getCommentsByNoteId']);// Route for retrieving all comments for a specific note
Route::post('/notes/{note_id}/comments', [NotesController::class, 'createCommentForNote']);// Route for creating a comment for a specific note

//routes for comments CRUD
Route::get('comments', [CommentsController::class, 'index']);// Route for retrieving all comments
Route::get('comments/{id}', [CommentsController::class, 'show']);// Route for retrieving a specific comment
Route::delete('comments/{id}', [CommentsController::class, 'destroy']);// Route for deleting a specific comment
Route::put('comments/{id}', [CommentsController::class, 'update']);// Route for updating a specific comment

//routes for hearts CRUD
Route::get('/notes/{note_id}/hearts/count', [HeartsController::class, 'countHeartsByNoteId']);// Route for retrieving the number of hearts for a specific note
Route::get('/comments/{comment_id}/hearts/count', [HeartsController::class, 'countHeartsByCommentId']);// Route for retrieving the number of hearts for a specific comment
Route::post('/notes/{note_id}/hearts', [HeartsController::class, 'createHeartForNote']);// Route for creating a heart (like) for a specific note
Route::post('/comments/{comment_id}/hearts', [HeartsController::class, 'createHeartForComment']);// Route for creating a heart (like) for a specific comment
Route::delete('/notes/{note_id}/hearts/{user_name}', [HeartsController::class, 'removeHeartFromNote']);// Route for removing a heart (unlike) for a specific note
Route::delete('/comments/{comment_id}/hearts/{user_name}', [HeartsController::class, 'removeHeartFromComment']);// Route for removing a heart (unlike) for a specific comment

//Route for Change Password
Route::post('change-password', [UsersController::class, 'changePassword'])->middleware('auth:sanctum');



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
