<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceMetric extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'officer_id',
        'emergency_request_id',
        'response_time',
        'distance_traveled',
        'metric_date',
        'metric_type',
        'rating',
        'feedback',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metric_date' => 'datetime',
        'response_time' => 'float',
        'distance_traveled' => 'float',
        'rating' => 'float',
    ];

    /**
     * Get the officer that owns this performance metric.
     */
    public function officer(): BelongsTo
    {
        return $this->belongsTo(Officer::class);
    }

    /**
     * Get the emergency request related to this performance metric.
     */
    public function emergencyRequest(): BelongsTo
    {
        return $this->belongsTo(EmergencyRequest::class);
    }
}
