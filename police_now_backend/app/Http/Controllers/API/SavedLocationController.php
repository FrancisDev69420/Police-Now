<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SavedLocation;
use Illuminate\Support\Facades\Auth;

class SavedLocationController extends Controller
{
    /**
     * Display a listing of the saved locations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Get the resident ID of the current user
        $residentId = Auth::user()->resident->id ?? null;
        
        if (!$residentId) {
            return response()->json(['message' => 'User is not a resident'], 400);
        }
        
        $savedLocations = SavedLocation::where('resident_id', $residentId)->get();
        
        return response()->json($savedLocations);
    }

    /**
     * Store a newly created saved location in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Get the resident ID of the current user
        $residentId = Auth::user()->resident->id ?? null;
        
        if (!$residentId) {
            return response()->json(['message' => 'User is not a resident'], 400);
        }
        
        $validated = $request->validate([
            'location_name' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_type' => 'nullable|string',
            'additional_info' => 'nullable|string',
        ]);
        
        $validated['resident_id'] = $residentId;

        $savedLocation = SavedLocation::create($validated);

        return response()->json($savedLocation, 201);
    }

    /**
     * Display the specified saved location.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $savedLocation = SavedLocation::findOrFail($id);
        
        // Check if the location belongs to the current user
        $residentId = Auth::user()->resident->id ?? null;
        
        if (!$residentId || $savedLocation->resident_id !== $residentId) {
            return response()->json(['message' => 'Unauthorized to access this location'], 403);
        }
        
        return response()->json($savedLocation);
    }

    /**
     * Update the specified saved location in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $savedLocation = SavedLocation::findOrFail($id);
        
        // Check if the location belongs to the current user
        $residentId = Auth::user()->resident->id ?? null;
        
        if (!$residentId || $savedLocation->resident_id !== $residentId) {
            return response()->json(['message' => 'Unauthorized to update this location'], 403);
        }

        $validated = $request->validate([
            'location_name' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'latitude' => 'sometimes|required|numeric',
            'longitude' => 'sometimes|required|numeric',
            'location_type' => 'nullable|string',
            'additional_info' => 'nullable|string',
        ]);

        $savedLocation->update($validated);

        return response()->json($savedLocation);
    }

    /**
     * Remove the specified saved location from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $savedLocation = SavedLocation::findOrFail($id);
        
        // Check if the location belongs to the current user
        $residentId = Auth::user()->resident->id ?? null;
        
        if (!$residentId || $savedLocation->resident_id !== $residentId) {
            return response()->json(['message' => 'Unauthorized to delete this location'], 403);
        }

        $savedLocation->delete();

        return response()->json(['message' => 'Saved location deleted successfully']);
    }
}
