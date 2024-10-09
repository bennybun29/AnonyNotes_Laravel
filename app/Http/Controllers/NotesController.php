<?php

namespace App\Http\Controllers;

use App\Models\Notes;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NotesController extends Controller
{
    
    public function index()
{
    // Retrieve all notes with only the required fields
    $notes = Notes::select('user_name', 'content', 'created_at')->get();

    // Return the notes as a JSON response
    return response()->json($notes);
}

public function store(Request $request)
{
    try {
        // Log the request data
        \Log::info('Request data: ', $request->all());

        // Validate the incoming request
        $request->validate([
            'user_name' => 'required|string',
            'content' => 'required|string',
            'anonymous' => 'required|boolean',
            'user_id' => 'required|integer|exists:users,user_id', // Ensure user_id is provided and exists
        ]);

        // Create a note with the provided data
        $noteData = $request->only(['user_name', 'content', 'anonymous']);
        $noteData['user_id'] = $request->user_id; // Assign user_id from request

        // Log the note data before creation
        \Log::info('Note data before creation: ', $noteData);

        // Create the note
        $note = Notes::create($noteData);

        return response()->json(['message' => "Note created successfully", 'note' => $note], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Handle validation errors
        \Log::error('Validation error: ', ['errors' => $e->errors()]);
        return response()->json(['errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        // Handle general errors
        \Log::error('Error creating note: ', ['error' => $e->getMessage(), 'request_data' => $request->all()]);
        return response()->json(['error' => 'Unable to create note'], 500);
    }
}



    public function show($id)
    {
        // Find and show a specific note by ID
        $note = Notes::find($id);

        if (!$note) {
            return response()->json(['message' => 'Note not found'], 404);
        }

        return response()->json($note);
    }

    public function update(Request $request, $id)
    {
        // Update the specified note
        $note = Notes::find($id);

        if (!$note) {
            return response()->json(['message' => 'Note not found'], 404);
        }

        $note->update($request->all());

        return response()->json($note);
    }

    public function destroy($id)
    {
        // Delete a note
        $note = Notes::find($id);

        if (!$note) {
            return response()->json(['message' => 'Note not found'], 404);
        }

        $note->delete();

        return response()->json(['message' => 'Note deleted']);
    }
}
