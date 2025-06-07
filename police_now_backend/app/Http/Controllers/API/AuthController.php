<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\UserRole;

class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'full_name' => 'required|string',
            'phone_number' => 'nullable|string',
            // Remove the role field from validation as we'll force it to be 'resident'
        ]);

        // Get the resident role ID
        $role = UserRole::where('role', 'resident')->first();
        if (!$role) {
            return response()->json(['message' => 'Resident role not found'], 500);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'role_id' => $role->id,
            'registration_date' => now(),
            'is_verified' => false,
            'verification_status' => 'pending',
            'address' => $request->address ?? null,
            'profile_image_url' => null,
        ]);        // Create the resident profile
        $user->resident()->create([
            'emergency_contact_name' => $request->emergency_contact_name ?? null,
            'emergency_contact_number' => $request->emergency_contact_number ?? null,
        ]);

        // Load the user's role relationship
        $user->load('role');

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * Login a user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check if user exists
        $user = User::where('username', $request->username)
                    ->orWhere('email', $request->username)
                    ->first();

        // Check if credentials are correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }        // Load the user's role relationship
        $user->load('role');

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Login an officer using badge number
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function officerLogin(Request $request)
    {
        $request->validate([
            'badge_number' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find officer by badge number
        $officer = \App\Models\Officer::where('badge_number', $request->badge_number)->first();

        if (!$officer) {
            throw ValidationException::withMessages([
                'badge_number' => ['Invalid badge number.'],
            ]);
        }

        // Get the user associated with this officer
        $user = $officer->user;

        // Check if password is correct
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Invalid password.'],
            ]);
        }

        // Check if user is verified and active
        if (!$user->is_verified) {
            return response()->json([
                'message' => 'Your account is pending verification. Please contact your administrator.'
            ], 403);
        }

        // Load relationships
        $user->load(['role', 'officer']);

        // Create new token
        $token = $user->createToken('officer_auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'officer' => $officer,
            'token' => $token
        ]);
    }

    /**
     * Get the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        // Load relationships based on user type
        if ($user->role->role === 'officer') {
            $user->load('officer');
        } elseif ($user->role->role === 'resident') {
            $user->load('resident');
        } elseif ($user->role->role === 'admin') {
            $user->load('admin');
        }
        
        return response()->json($user);
    }

    /**
     * Logout a user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['message' => 'Successfully logged out']);
    }
}
