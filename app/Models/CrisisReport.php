<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrisisReport extends Model
{
    protected $fillable = [
        'user_id',
        'description',
        'status',
        'responded_by',
        'responded_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
            'resolved_at'  => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
}
