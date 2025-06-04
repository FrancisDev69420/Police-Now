<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CommunicationLog;
use App\Models\EmergencyRequest;
use Illuminate\Support\Facades\Auth;

class CommunicationLogController extends Controller
{
    /**
     * Display a listing of the communication logs.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = CommunicationLog::with(['sender', 'emergencyRequest']);

        // Filter by emergency request
        if ($request->has('emergency_request_id')) {
            $query->where('emergency_request_id', $request->emergency_request_id);
        }
        
        // Filter by sender
        if ($request->has('sender_id')) {
            $query->where('sender_id', $request->sender_id);
        }
        
        $logs = $query->orderBy('timestamp', 'desc')->get();
        
        return response()->json($logs);
    }

    /**
     * Store a newly created communication log in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'emergency_request_id' => 'required|exists:emergency_requests,id',
            'message_content' => 'required|string',
            'message_type' => 'required|string',
            'is_emergency' => 'boolean',
            'attachment_url' => 'nullable|string',
        ]);

        // Set the current authenticated user as the sender
        $validated['sender_id'] = Auth::id();
        $validated['timestamp'] = now();

        $log = CommunicationLog::create($validated);

        return response()->json($log, 201);
    }

    /**
     * Display the specified communication log.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $log = CommunicationLog::with(['sender', 'emergencyRequest'])->findOrFail($id);
        return response()->json($log);
    }

    /**
     * Update the specified communication log in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $log = CommunicationLog::findOrFail($id);

        // Only the sender can update their message
        if ($log->sender_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized to update this message'], 403);
        }

        $validated = $request->validate([
            'message_content' => 'sometimes|required|string',
            'message_type' => 'sometimes|required|string',
            'is_emergency' => 'sometimes|boolean',
            'attachment_url' => 'nullable|string',
        ]);

        $log->update($validated);

        return response()->json($log);
    }

    /**
     * Remove the specified communication log from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $log = CommunicationLog::findOrFail($id);
        
        // Only the sender or an admin can delete a message
        if ($log->sender_id !== Auth::id() && Auth::user()->role->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized to delete this message'], 403);
        }

        $log->delete();

        return response()->json(['message' => 'Communication log deleted successfully']);
    }
}
