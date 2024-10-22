<?php

namespace App\Http\Controllers;
use App\Models\Comments;
use App\Models\Notes;
use App\Models\Hearts;
use Illuminate\Http\Request;

class HeartsController extends Controller
{
    
    
    //Counts the number of hearts associated with a given note_id and returns the count 
    public function countHeartsByNoteId($note_id)
    {
        // Count the number of hearts associated with the given note_id
        $heartCount = Hearts::where('note_id', $note_id)->count();

        // Return the count of hearts
        return response()->json(['note_id' => $note_id, 'heart_count' => $heartCount], 200);
    }

    //Counts the number of hearts associated with a given comment_id and returns the count
    public function countHeartsByCommentId($comment_id)
    {
        // Count the number of hearts associated with the given comment_id
        $heartCount = Hearts::where('comment_id', $comment_id)->count();

        // Return the count of hearts
        return response()->json(['comment_id' => $comment_id, 'heart_count' => $heartCount], 200);
    }

    // Function to create a heart (like) for a specific note
    public function createHeartForNote(Request $request, $note_id)
    {
        // Validate the request input
        $validated = $request->validate([
            'user_name' => 'required|exists:users,user_name', // Ensure the user exists
        ]);

        // Create a new heart
        Hearts::create([
            'note_id' => $note_id,
            'user_name' => $validated['user_name'],
            'created_at' => now(),
        ]);

        return response()->json(['message' => 'Heart added to the note successfully!'], 201);
    }

    // Function to create a heart (like) for a specific comment
    public function createHeartForComment(Request $request, $comment_id)
    {
        // Validate the request input
        $validated = $request->validate([
            'user_name' => 'required|exists:users,user_name', // Ensure the user exists
        ]);

        // Create a new heart
        Hearts::create([
            'comment_id' => $comment_id,
            'user_name' => $validated['user_name'],
            'created_at' => now(),
        ]);

        return response()->json(['message' => 'Heart added to the comment successfully!'], 201);
    }
    
    // Function to remove a heart (unlike) for a specific note
    public function removeHeartFromNote($note_id, $user_name)
    {
        // Find and delete the heart by note_id and user_name
        $heart = Hearts::where('note_id', $note_id)
                    ->where('user_name', $user_name)
                    ->first();

        if ($heart) {
            $heart->delete();
            return response()->json(['message' => 'Heart removed from the note successfully!'], 200);
        } else {
            return response()->json(['message' => 'Heart not found for this note.'], 404);
        }
    }

    // Function to remove a heart (unlike) for a specific comment
    public function removeHeartFromComment($comment_id, $user_name)
    {
        // Find and delete the heart by comment_id and user_name
        $heart = Hearts::where('comment_id', $comment_id)
                    ->where('user_name', $user_name)
                    ->first();

        if ($heart) {
            $heart->delete();
            return response()->json(['message' => 'Heart removed from the comment successfully!'], 200);
        } else {
            return response()->json(['message' => 'Heart not found for this comment.'], 404);
        }
    }


}
