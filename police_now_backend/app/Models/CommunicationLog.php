<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'emergency_request_id',
        'sender_id',
        'message_content',
        'timestamp',
        'message_type',
        'is_emergency',
        'attachment_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'timestamp' => 'datetime',
        'is_emergency' => 'boolean',
    ];

    /**
     * Get the emergency request that owns this communication log.
     */
    public function emergencyRequest(): BelongsTo
    {
        return $this->belongsTo(EmergencyRequest::class);
    }

    /**
     * Get the user who sent this message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
