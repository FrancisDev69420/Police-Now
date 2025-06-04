<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmergencyRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'resident_id',
        'emergency_type',
        'status',
        'request_time',
        'response_time',
        'request_latitude',
        'request_longitude',
        'location_details',
        'priority_level',
        'additional_notes',
        'is_resolved',
        'resolution_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'request_time' => 'datetime',
        'response_time' => 'datetime',
        'resolution_time' => 'datetime',
        'request_latitude' => 'float',
        'request_longitude' => 'float',
        'is_resolved' => 'boolean',
    ];

    /**
     * Get the resident that owns this emergency request.
     */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    /**
     * Get the officer assignments for this emergency request.
     */
    public function officerAssignments(): HasMany
    {
        return $this->hasMany(OfficerAssignment::class);
    }

    /**
     * Get the evidence files for this emergency request.
     */
    public function evidenceFiles(): HasMany
    {
        return $this->hasMany(EvidenceFile::class);
    }

    /**
     * Get the communication logs for this emergency request.
     */
    public function communicationLogs(): HasMany
    {
        return $this->hasMany(CommunicationLog::class);
    }
}
