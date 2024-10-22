<?php

namespace App\Http\Controllers;
use App\Models\Comments;
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

// Function to fetch all comments for a specific note
public function getCommentsByNoteId(int $noteId)
{
    // Check if the note exists
    $note = Notes::find($noteId);
    if ($note === null) {
        return response()->json(['message' => 'Note not found'], 404);
    }

    // Fetch all comments related to the note
    $comments = Comments::where('note_id', $noteId)->get();
    if ($comments->isEmpty()) {
        return response()->json(['message' => 'Comments not found'], 404);
    }

    // Return the comments in a JSON response
    return response()->json($comments, 200);
}

// Function to create a comment for a specific note
public function createCommentForNote(Request $request, int $noteId)
{
    try {
        // Validate the request data
        $validatedData = $request->validate([
            'user_name' => 'required|string',
            'content' => 'required|string',
            'anonymous' => 'required|boolean',
        ]);

        // Retrieve the note by its ID
        $note = Notes::find($noteId);

        // Check if the note exists
        if (!$note) {
            return response()->json(['message' => 'Note not found'], 404);
        }

        // Create a new comment
        $comment = Comments::create([
            'note_id' => $noteId,
            'user_name' => $validatedData['user_name'],
            'content' => $validatedData['content'],
            'anonymous' => $validatedData['anonymous'],
        ]);

        // Return the newly created comment with a 201 status
        return response()->json(['message' => 'Comment created successfully!', 'comment' => $comment], 201);
    } catch (ValidationException $e) {
        return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        return response()->json(['message' => 'An error occurred while creating the comment'], 500);
    }
}

// Function to create a new note
public function store(Request $request)
{
    try {
        $validatedData = $request->validate([
            'user_name' => 'required|string',
            'content' => 'required|string',
            'anonymous' => 'required|boolean',
            'user_id' => 'required|integer|exists:users,user_id',
        ]);

        $note = Notes::create($validatedData);

        return response()->json(['message' => 'Note created successfully', 'note' => $note], 201);
    } catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    } catch (Exception $e) {
        Log::error('Error creating note', [
            'error' => $e->getMessage(),
            'request_data' => $request->all(),
        ]);

        return response()->json(['error' => 'Unable to create note'], 500);
    }
}
// Function to show a specific note
    public function show($id)
    {
        // Find and show a specific note by ID
        $note = Notes::find($id);

        if (!$note) {
            return response()->json(['message' => 'Note not found'], 404);
        }

        return response()->json($note);
    }

    // Function to update a specific note
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
