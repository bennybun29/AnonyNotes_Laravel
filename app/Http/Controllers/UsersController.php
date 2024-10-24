<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    /**
     * Register user.
     */
    public function register(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'user_name' => 'required|string|max:50|unique:users',
                'email' => 'required|string|email|max:50|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Create a new user and hash the password
            $user = Users::create([
                'user_name' => $validatedData['user_name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']), // Hash the password
                'created_at' => now(),
            ]);

            // Generate an API token using Laravel Sanctum
            if ($user !== null) {
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'message' => 'User registered successfully',
                    'token' => $token,
                    'user_id' => $user->user_id,  // Include user_id here
                    'user' => $user,
                    'created_at' => $user->created_at,
                ], 201);
            }

            return response()->json([
                'message' => 'Could not register the user, please try again later.',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while registering the user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * User login and token generation.
     */
    public function login(Request $request)
    {
        // Validate the login credentials
        $validatedData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate the user
        $user = Users::where('email', $validatedData['email'])->first();

        // Check if the user exists and the password matches
        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()->json([
                'message' => 'The provided email / password is incorrect.',
            ]);
        }

        // Generate an API token using Laravel Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'username' => $user->user_name,
            'user_id' => $user->user_id,  // Include user_id here
            'created_at' => $user->created_at,
        ]);
    }

    /**
     * Display a listing of all users.
     */
    public function index()
    {
        // Retrieve all users
        $users = Users::all();

        return response()->json($users);
    }

    /**
     * Show a specific user.
     */
    public function show($id)
    {
        // Find the user by ID
        $user = Users::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    /**
     * Update a specific user's details.
     */
    public function update(Request $request, $id)
    {
        // Find the user by ID
        $user = Users::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'user_name' => 'sometimes|required|string|max:50|unique:users,user_name,' . $id . ',user_id',
            'email' => 'sometimes|required|string|email|max:50|unique:users,email,' . $id . ',user_id',
            'password' => 'sometimes|string|min:8|confirmed',
            'bio' => 'nullable|string|max:500',
            'profile_img' => 'nullable|image|max:2048',
        ]);

        // Update the user attributes
        if ($request->has('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);

        return response()->json(['message' => 'User updated successfully']);
    }

    public function changePassword(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
    
        try {
            // Get the currently authenticated user
            $user = $request->user(); // Use Laravel's method to get the user based on token
    
            // Check if the current password matches the hashed password in the database
            if (!Hash::check($validatedData['current_password'], $user->password)) {
                return response()->json(['success' => false, 'message' => 'Current password is incorrect'], 400);
            }
    
            // Update the user's password
            $user->password = Hash::make($validatedData['new_password']);
            $user->save();
    
            return response()->json(['success' => true, 'message' => 'Password changed successfully'], 200);
    
        } catch (\Exception $e) {
            Log::error('Password change error: ', [
                'request' => $request->all(),
                'error' => $e->getMessage()
            ]); // Log the error
            return response()->json(['success' => false, 'error' => 'An error occurred.'], 500);
        }
    }
    


    /**
     * Delete a specific user.
     */
    public function destroy($id)
    {
        // Find the user by ID and delete
        $user = Users::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
    
    public function getUserNotes($id)
    {
        // Find the user by ID
        $user = Users::find($id);
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        // Retrieve all notes for the user by user_id
        $notes = $user->notes;
    
        return response()->json($notes);
    }

        public function getUserBio($id)
    {
        // Find the user by ID
        $user = Users::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Return the user's bio (assuming the bio column exists)
        return response()->json(['bio' => $user->bio], 200);
    }

}
