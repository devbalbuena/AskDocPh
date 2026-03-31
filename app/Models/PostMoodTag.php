<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostMoodTag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'post_id',
        'mood_tag_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function moodTag(): BelongsTo
    {
        return $this->belongsTo(MoodTag::class);
    }
}
