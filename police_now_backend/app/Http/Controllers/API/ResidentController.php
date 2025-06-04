<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resident;
use App\Models\User;

class ResidentController extends Controller
{
    /**
     * Display a listing of the residents.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $residents = Resident::with('user')->get();
        return response()->json($residents);
    }

    /**
     * Store a newly created resident in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:residents,user_id',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_number' => 'nullable|string',
            'medical_info' => 'nullable|string',
            'residential_address' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'postal_code' => 'nullable|string',
        ]);

        $resident = Resident::create($validated);

        return response()->json($resident, 201);
    }

    /**
     * Display the specified resident.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $resident = Resident::with('user')->findOrFail($id);
        return response()->json($resident);
    }

    /**
     * Update the specified resident in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $resident = Resident::findOrFail($id);

        $validated = $request->validate([
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_number' => 'nullable|string',
            'medical_info' => 'nullable|string',
            'residential_address' => 'nullable|string',
            'city' => 'nullable|string',
            'province' => 'nullable|string',
            'postal_code' => 'nullable|string',
        ]);

        $resident->update($validated);

        return response()->json($resident);
    }

    /**
     * Remove the specified resident from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $resident = Resident::findOrFail($id);
        $resident->delete();

        return response()->json(['message' => 'Resident deleted successfully']);
    }
}
