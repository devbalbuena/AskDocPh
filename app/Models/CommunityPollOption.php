<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityPollOption extends Model
{
    protected $fillable = [
        'poll_id',
        'text',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(CommunityPoll::class, 'poll_id');
    }

    public function votes()
    {
        return $this->hasMany(CommunityPollVote::class, 'option_id');
    }
}
