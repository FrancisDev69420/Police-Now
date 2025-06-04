<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resident extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'emergency_contact_name',
        'emergency_contact_number',
        'medical_info',
        'residential_address',
        'city',
        'province',
        'postal_code',
    ];

    /**
     * Get the user that owns this resident profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the emergency requests for this resident.
     */
    public function emergencyRequests(): HasMany
    {
        return $this->hasMany(EmergencyRequest::class);
    }

    /**
     * Get the saved locations for this resident.
     */
    public function savedLocations(): HasMany
    {
        return $this->hasMany(SavedLocation::class);
    }
}
