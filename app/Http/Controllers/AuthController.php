<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Create a function that register a user
    public function register(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string'
        ]);

        // Create a user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            // Hash the password
            'password' => Hash::make($request->password)
        ]);

        // Create a token for the user
        // $token = $user->createToken('auth_token')->plainTextToken;

        $token = $user->createToken('auth_token', ['user_id' => $user->id])->plainTextToken;
        // Return the response
        return response(['message' => 'User registered successfully', 'user' => $user, 'token' => $token], 201);
    }

    // Create a function that login a user
    public function login(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Check if the user exists
        $user = User::where('email', $request->email)->first();

        // If the user doesn't exist, return an error
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        // If the user exists, check if the password is correct
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        // If the password is correct, create a token for the user
        // $token = $user->createToken('auth_token')->plainTextToken;
        $token = $user->createToken('auth_token', ['user_id' => $user->id])->plainTextToken;

        // Return the response
        return response(['message' => 'User logged in successfully', 'user' => $user, 'token' => $token], 200);
        // return response(['message' => 'User logged in successfully', 'user' => $user, 'token' => $token], 200);
    }

    // Create a function that logout a user
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        // Return the response
        return response(['message' => 'User logged out successfully'], 200);
    }
}
