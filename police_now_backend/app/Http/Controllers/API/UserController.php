<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::with('role')->get();
        return response()->json($users);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'full_name' => 'required|string',
            'phone_number' => 'nullable|string',
            'role_id' => 'required|exists:user_roles,id',
            'address' => 'nullable|string',
            'profile_image_url' => 'nullable|image|max:2048',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image_url')) {
            $path = $request->file('profile_image_url')->store('profile_images', 'public');
            $validated['profile_image_url'] = Storage::url($path);
        }

        // Hash password
        $validated['password'] = Hash::make($validated['password']);
        $validated['registration_date'] = now();
        
        $user = User::create($validated);

        return response()->json($user, 201);
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => 'sometimes|required|string|unique:users,username,' . $id,
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'full_name' => 'sometimes|required|string',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'profile_image_url' => 'nullable|image|max:2048',
            'is_verified' => 'sometimes|boolean',
            'verification_status' => 'sometimes|string',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image_url')) {
            // Delete old image if exists
            if ($user->profile_image_url) {
                $oldPath = str_replace('/storage/', '', $user->profile_image_url);
                Storage::disk('public')->delete($oldPath);
            }
            
            // Upload new image
            $path = $request->file('profile_image_url')->store('profile_images', 'public');
            $validated['profile_image_url'] = Storage::url($path);
        }

        $user->update($validated);

        return response()->json($user);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Delete profile image if exists
        if ($user->profile_image_url) {
            $path = str_replace('/storage/', '', $user->profile_image_url);
            Storage::disk('public')->delete($path);
        }
        
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
