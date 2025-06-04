<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmergencyRequest;
use App\Models\Officer;
use App\Models\OfficerAssignment;

class EmergencyRequestController extends Controller
{
    /**
     * Display a listing of the emergency requests.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = EmergencyRequest::with(['resident.user', 'officerAssignments.officer.user']);
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('request_time', [$request->from_date, $request->to_date]);
        }
        
        $emergencyRequests = $query->get();
        
        return response()->json($emergencyRequests);
    }

    /**
     * Store a newly created emergency request in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'emergency_type' => 'required|string',
            'request_latitude' => 'required|numeric',
            'request_longitude' => 'required|numeric',
            'location_details' => 'nullable|string',
            'priority_level' => 'required|string|in:low,medium,high,critical',
            'additional_notes' => 'nullable|string',
        ]);

        $validated['status'] = 'pending';
        $validated['request_time'] = now();
        $validated['is_resolved'] = false;

        $emergencyRequest = EmergencyRequest::create($validated);

        // Find available officers to respond
        $availableOfficers = Officer::where('on_duty', true)
                                    ->where('status', 'active')
                                    ->get();
                                    
        // Auto-assign nearest officer logic could be implemented here
                                    
        return response()->json($emergencyRequest, 201);
    }

    /**
     * Display the specified emergency request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $emergencyRequest = EmergencyRequest::with([
            'resident.user', 
            'officerAssignments.officer.user',
            'evidenceFiles',
            'communicationLogs.sender'
        ])->findOrFail($id);
        
        return response()->json($emergencyRequest);
    }

    /**
     * Update the specified emergency request in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $emergencyRequest = EmergencyRequest::findOrFail($id);

        $validated = $request->validate([
            'emergency_type' => 'sometimes|required|string',
            'status' => 'sometimes|required|string|in:pending,in_progress,resolved,cancelled',
            'priority_level' => 'sometimes|required|string|in:low,medium,high,critical',
            'location_details' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'is_resolved' => 'sometimes|boolean',
            'resolution_time' => 'nullable|date',
        ]);

        // If status is changed to resolved, update related fields
        if (isset($validated['is_resolved']) && $validated['is_resolved'] && !$emergencyRequest->is_resolved) {
            $validated['resolution_time'] = now();
        }

        $emergencyRequest->update($validated);

        return response()->json($emergencyRequest);
    }

    /**
     * Remove the specified emergency request from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $emergencyRequest = EmergencyRequest::findOrFail($id);
        $emergencyRequest->delete();

        return response()->json(['message' => 'Emergency request deleted successfully']);
    }

    /**
     * Update the status of an emergency request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $emergencyRequest = EmergencyRequest::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:pending,in_progress,resolved,cancelled',
            'additional_notes' => 'nullable|string',
        ]);

        // If status is changed to resolved, update related fields
        if ($validated['status'] === 'resolved' && $emergencyRequest->status !== 'resolved') {
            $emergencyRequest->is_resolved = true;
            $emergencyRequest->resolution_time = now();
        }

        if ($request->has('additional_notes')) {
            $emergencyRequest->additional_notes = $request->additional_notes;
        }

        $emergencyRequest->status = $validated['status'];
        $emergencyRequest->save();

        if ($validated['status'] === 'in_progress' && $request->has('officer_id')) {
            // Create or update officer assignment
            $officerAssignment = OfficerAssignment::updateOrCreate(
                ['emergency_request_id' => $emergencyRequest->id, 'officer_id' => $request->officer_id],
                [
                    'assignment_time' => now(),
                    'assignment_status' => 'assigned'
                ]
            );
        }

        return response()->json($emergencyRequest);
    }
}
