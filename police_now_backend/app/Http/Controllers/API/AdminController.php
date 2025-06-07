<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Officer;
use App\Models\Resident;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Create a new officer account
     * This endpoint should be protected by admin middleware
     */
    public function createOfficer(Request $request)
    {
        // Validate request
        $request->validate([
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'full_name' => 'required|string',
            'phone_number' => 'nullable|string',
            'badge_number' => 'required|string|unique:officers',
            'rank' => 'required|string',
            'department' => 'required|string',
            'specialization' => 'nullable|string',
            'service_start_date' => 'required|date',
        ]);

        // Get officer role
        $role = UserRole::where('role', 'officer')->first();
        if (!$role) {
            return response()->json(['message' => 'Officer role not found'], 500);
        }

        // Generate a random password that the officer will change on first login
        $tempPassword = Str::random(12);

        // Create the user
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($tempPassword),
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'role_id' => $role->id,
            'registration_date' => now(),
            'is_verified' => true, // Admin-created accounts are pre-verified
            'verification_status' => 'verified',
            'address' => $request->address ?? null,
            'profile_image_url' => null,
        ]);

        // Create the officer profile
        $officer = $user->officer()->create([
            'badge_number' => $request->badge_number,
            'rank' => $request->rank,
            'department' => $request->department,
            'status' => 'active',
            'service_start_date' => $request->service_start_date,
            'specialization' => $request->specialization,
            'on_duty' => false,
        ]);

        // TODO: Send email to officer with their login details and temporary password

        return response()->json([
            'message' => 'Officer created successfully',
            'user' => $user,
            'officer' => $officer,
            'temporary_password' => $tempPassword // In production, you would NOT return this and only email it
        ], 201);
    }
    
    /**
     * Get all officers
     */
    public function getAllOfficers()
    {
        $officers = User::whereHas('role', function($query) {
            $query->where('role', 'officer');
        })->with('officer')->get();
        
        return response()->json($officers);
    }
    
    /**
     * Update officer details
     */
    public function updateOfficer(Request $request, $id)
    {
        $officer = Officer::where('id', $id)->first();
        
        if (!$officer) {
            return response()->json(['message' => 'Officer not found'], 404);
        }
        
        // Update officer and related user details
        if ($request->has('badge_number')) $officer->badge_number = $request->badge_number;
        if ($request->has('rank')) $officer->rank = $request->rank;
        if ($request->has('department')) $officer->department = $request->department;
        if ($request->has('status')) $officer->status = $request->status;
        if ($request->has('specialization')) $officer->specialization = $request->specialization;
        
        $officer->save();
        
        // Update user details if provided
        $user = $officer->user;
        if ($request->has('full_name')) $user->full_name = $request->full_name;
        if ($request->has('phone_number')) $user->phone_number = $request->phone_number;
        if ($request->has('address')) $user->address = $request->address;
        
        $user->save();
        
        return response()->json([
            'message' => 'Officer details updated',
            'officer' => $officer->load('user')
        ]);
    }
    
    /**
     * Delete an officer account
     */
    public function deleteOfficer($id)
    {
        $officer = Officer::find($id);
        
        if (!$officer) {
            return response()->json(['message' => 'Officer not found'], 404);
        }
        
        // Get the user associated with this officer
        $user = $officer->user;
        
        // Delete the officer first (child record)
        $officer->delete();
        
        // Then delete the user (parent record)
        $user->delete();
        
        return response()->json(['message' => 'Officer account deleted successfully']);
    }

    /**
     * Get all residents
     */
    public function getAllResidents()
    {
        try {
            // Get the resident role ID
            $residentRole = UserRole::where('role', 'resident')->first();
            
            if (!$residentRole) {
                \Log::error('Resident role not found in database');
                return response()->json(['message' => 'Resident role not found'], 404);
            }
            
            \Log::info('Found resident role with ID: ' . $residentRole->id);
            
            // Get all users with resident role
            $users = User::where('role_id', $residentRole->id)->get();
            \Log::info('Found ' . $users->count() . ' users with resident role');
            
            // Get all resident profiles
            $residents = Resident::with('user')->get();
            \Log::info('Found ' . $residents->count() . ' resident profiles');
            
            // If no residents found, return empty array with message
            if ($residents->isEmpty()) {
                return response()->json([
                    'message' => 'No residents found',
                    'data' => [],
                    'debug_info' => [
                        'resident_role_id' => $residentRole->id,
                        'users_with_role' => $users->count(),
                        'resident_profiles' => $residents->count()
                    ]
                ]);
            }
            
            // Transform the data to include all necessary information
            $formattedResidents = $residents->map(function ($resident) {
                return [
                    'id' => $resident->id,
                    'user_id' => $resident->user_id,
                    'username' => $resident->user->username,
                    'email' => $resident->user->email,
                    'full_name' => $resident->user->full_name,
                    'phone_number' => $resident->user->phone_number,
                    'address' => $resident->user->address,
                    'emergency_contact_name' => $resident->emergency_contact_name,
                    'emergency_contact_number' => $resident->emergency_contact_number,
                    'medical_info' => $resident->medical_info,
                    'residential_address' => $resident->residential_address,
                    'city' => $resident->city,
                    'province' => $resident->province,
                    'postal_code' => $resident->postal_code,
                    'is_verified' => $resident->user->is_verified,
                    'verification_status' => $resident->user->verification_status,
                    'registration_date' => $resident->user->registration_date,
                    'created_at' => $resident->created_at,
                    'updated_at' => $resident->updated_at
                ];
            });
            
            return response()->json([
                'message' => 'Residents retrieved successfully',
                'data' => $formattedResidents,
                'debug_info' => [
                    'resident_role_id' => $residentRole->id,
                    'users_with_role' => $users->count(),
                    'resident_profiles' => $residents->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching residents: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error fetching residents',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update resident details
     */
    public function updateResident(Request $request, $id)
    {
        $resident = Resident::where('id', $id)->first();
        
        if (!$resident) {
            return response()->json(['message' => 'Resident not found'], 404);
        }
        
        // Update resident and related user details
        if ($request->has('emergency_contact_name')) $resident->emergency_contact_name = $request->emergency_contact_name;
        if ($request->has('emergency_contact_number')) $resident->emergency_contact_number = $request->emergency_contact_number;
        if ($request->has('medical_info')) $resident->medical_info = $request->medical_info;
        
        $resident->save();
        
        // Update user details if provided
        $user = $resident->user;
        if ($request->has('full_name')) $user->full_name = $request->full_name;
        if ($request->has('phone_number')) $user->phone_number = $request->phone_number;
        if ($request->has('address')) $user->address = $request->address;
        if ($request->has('is_verified')) $user->is_verified = $request->is_verified;
        if ($request->has('verification_status')) $user->verification_status = $request->verification_status;
        
        $user->save();
        
        return response()->json([
            'message' => 'Resident details updated',
            'resident' => $resident->load('user')
        ]);
    }
    
    /**
     * Delete a resident account
     */
    public function deleteResident($id)
    {
        $resident = Resident::find($id);
        
        if (!$resident) {
            return response()->json(['message' => 'Resident not found'], 404);
        }
        
        // Get the user associated with this resident
        $user = $resident->user;
        
        // Delete the resident first (child record)
        $resident->delete();
        
        // Then delete the user (parent record)
        $user->delete();
        
        return response()->json(['message' => 'Resident account deleted successfully']);
    }
}