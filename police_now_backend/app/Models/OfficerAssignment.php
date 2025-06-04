<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficerAssignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'emergency_request_id',
        'officer_id',
        'assignment_time',
        'assignment_status',
        'arrival_time',
        'notes',
        'distance_to_incident',
        'estimated_arrival_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'assignment_time' => 'datetime',
        'arrival_time' => 'datetime',
        'estimated_arrival_time' => 'datetime',
        'distance_to_incident' => 'float',
    ];

    /**
     * Get the emergency request that this assignment belongs to.
     */
    public function emergencyRequest(): BelongsTo
    {
        return $this->belongsTo(EmergencyRequest::class);
    }

    /**
     * Get the officer that owns this assignment.
     */
    public function officer(): BelongsTo
    {
        return $this->belongsTo(Officer::class);
    }
}
