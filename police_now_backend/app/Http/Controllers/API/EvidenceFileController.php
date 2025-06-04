<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EvidenceFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class EvidenceFileController extends Controller
{
    /**
     * Display a listing of the evidence files.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = EvidenceFile::query();

        if ($request->has('emergency_request_id')) {
            $query->where('emergency_request_id', $request->emergency_request_id);
        }
        
        $evidenceFiles = $query->get();
        
        return response()->json($evidenceFiles);
    }

    /**
     * Upload and store a new evidence file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $request->validate([
            'emergency_request_id' => 'required|exists:emergency_requests,id',
            'file' => 'required|file|max:50000', // 50MB max file size
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'timestamp' => 'nullable|date',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('evidence_files', 'public');
            
            $evidenceFile = new EvidenceFile();
            $evidenceFile->emergency_request_id = $request->emergency_request_id;
            $evidenceFile->file_url = Storage::url($path);
            $evidenceFile->file_type = $file->getMimeType();
            $evidenceFile->timestamp = $request->timestamp ?? now();
            $evidenceFile->latitude = $request->latitude;
            $evidenceFile->longitude = $request->longitude;
            $evidenceFile->description = $request->description;
            $evidenceFile->is_verified = false;
            $evidenceFile->metadata = json_encode([
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'uploaded_by' => Auth::id(),
                'upload_time' => now()->toDateTimeString(),
            ]);
            
            $evidenceFile->save();
            
            return response()->json($evidenceFile, 201);
        }
        
        return response()->json(['message' => 'No file uploaded'], 400);
    }

    /**
     * Store a newly created evidence file record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'emergency_request_id' => 'required|exists:emergency_requests,id',
            'file_url' => 'required|string',
            'file_type' => 'required|string',
            'timestamp' => 'required|date',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_verified' => 'boolean',
            'description' => 'nullable|string',
            'metadata' => 'nullable|string',
        ]);

        $evidenceFile = EvidenceFile::create($validated);

        return response()->json($evidenceFile, 201);
    }

    /**
     * Display the specified evidence file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $evidenceFile = EvidenceFile::findOrFail($id);
        return response()->json($evidenceFile);
    }

    /**
     * Update the specified evidence file in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $evidenceFile = EvidenceFile::findOrFail($id);

        $validated = $request->validate([
            'file_type' => 'sometimes|string',
            'timestamp' => 'sometimes|date',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_verified' => 'sometimes|boolean',
            'description' => 'nullable|string',
            'metadata' => 'nullable|string',
        ]);

        $evidenceFile->update($validated);

        return response()->json($evidenceFile);
    }

    /**
     * Remove the specified evidence file from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $evidenceFile = EvidenceFile::findOrFail($id);
        
        // Remove the actual file
        $path = str_replace('/storage/', '', $evidenceFile->file_url);
        Storage::disk('public')->delete($path);
        
        $evidenceFile->delete();

        return response()->json(['message' => 'Evidence file deleted successfully']);
    }
}
