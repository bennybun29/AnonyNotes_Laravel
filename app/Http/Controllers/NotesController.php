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
    // Validate the request data
    $request->validate([
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
    $comment = new Comments();
    $comment->note_id = $noteId;
    $comment->user_name = $note->user_name; // Use the user_name from the note
    $comment->content = $request->content;
    $comment->anonymous = $request->anonymous;

    // Save the comment
    if ($comment->save()) {
        return response()->json(['message' => 'Comment created successfully!'], 201);
    }

    return response()->json(['message' => 'Failed to create comment'], 500);
}

    /**
     * Create a new note with the given data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
