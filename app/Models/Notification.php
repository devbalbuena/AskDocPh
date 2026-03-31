<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Custom Notification model for the notifications table.
 *
 * NOTE: This is NOT the same as Illuminate\Notifications\DatabaseNotification.
 * It maps to our custom `notifications` table with actor_id + json data.
 * Do NOT use Laravel's built-in Notifiable trait's notification() relationship
 * with this model — use User::notifications() hasMany instead.
 */
class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'actor_id',
        'type',
        'data',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'data'    => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
