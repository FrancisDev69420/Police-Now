<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Officer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'badge_number',
        'rank',
        'department',
        'status',
        'service_start_date',
        'shift_start',
        'shift_end',
        'specialization',
        'on_duty',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'service_start_date' => 'datetime',
        'shift_start' => 'datetime',
        'shift_end' => 'datetime',
        'on_duty' => 'boolean',
    ];

    /**
     * Get the user that owns this officer profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assignments for this officer.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(OfficerAssignment::class);
    }

    /**
     * Get the performance metrics for this officer.
     */
    public function performanceMetrics(): HasMany
    {
        return $this->hasMany(PerformanceMetric::class);
    }
}
