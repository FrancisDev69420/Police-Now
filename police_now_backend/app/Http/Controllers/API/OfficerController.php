<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Officer;
use App\Models\User;

class OfficerController extends Controller
{
    /**
     * Display a listing of the officers.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $officers = Officer::with('user')->get();
        return response()->json($officers);
    }

    /**
     * Store a newly created officer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:officers,user_id',
            'badge_number' => 'required|string|unique:officers',
            'rank' => 'required|string',
            'department' => 'required|string',
            'status' => 'required|string',
            'service_start_date' => 'required|date',
            'shift_start' => 'nullable|date',
            'shift_end' => 'nullable|date',
            'specialization' => 'nullable|string',
            'on_duty' => 'boolean',
        ]);

        $officer = Officer::create($validated);

        return response()->json($officer, 201);
    }

    /**
     * Display the specified officer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $officer = Officer::with('user')->findOrFail($id);
        return response()->json($officer);
    }

    /**
     * Update the specified officer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $officer = Officer::findOrFail($id);

        $validated = $request->validate([
            'badge_number' => 'sometimes|required|string|unique:officers,badge_number,' . $id,
            'rank' => 'sometimes|required|string',
            'department' => 'sometimes|required|string',
            'status' => 'sometimes|required|string',
            'service_start_date' => 'sometimes|required|date',
            'shift_start' => 'nullable|date',
            'shift_end' => 'nullable|date',
            'specialization' => 'nullable|string',
            'on_duty' => 'sometimes|boolean',
        ]);

        $officer->update($validated);

        return response()->json($officer);
    }

    /**
     * Remove the specified officer from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $officer = Officer::findOrFail($id);
        $officer->delete();

        return response()->json(['message' => 'Officer deleted successfully']);
    }

    /**
     * Update officer duty status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDutyStatus(Request $request, $id)
    {
        $officer = Officer::findOrFail($id);

        $validated = $request->validate([
            'on_duty' => 'required|boolean',
            'shift_start' => 'nullable|date',
            'shift_end' => 'nullable|date',
        ]);

        if ($validated['on_duty'] && !$officer->on_duty) {
            // Officer is going on duty
            $validated['shift_start'] = now();
            $validated['shift_end'] = null;
        } elseif (!$validated['on_duty'] && $officer->on_duty) {
            // Officer is going off duty
            $validated['shift_end'] = now();
        }

        $officer->update($validated);

        return response()->json($officer);
    }
}
