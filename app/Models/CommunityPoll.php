<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunityPoll extends Model
{
    protected $fillable = [
        'user_id',
        'group_id',
        'question',
        'ends_at',
    ];

    protected $casts = [
        'ends_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(CommunityPollOption::class, 'poll_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(CommunityPollVote::class, 'poll_id');
    }

    public function hasVoted(int $userId): bool
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    public function totalVotes(): int
    {
        return $this->votes()->count();
    }
}
