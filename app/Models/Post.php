<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Post extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'user_id',
        'post_type',
        'text_content',
        'link_url',
        'group_id',
        'resource_id',
        'share_count',
        'share_notes',
    ];

    protected function casts(): array
    {
        return [
            'share_count' => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function resource(): BelongsTo
    {
        // Aliased as HealthResource in controllers to avoid conflict with Laravel's JsonResource
        return $this->belongsTo(Resource::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(PostMedia::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class)->whereNull('parent_comment_id');
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(PostShare::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(PostBookmark::class);
    }

    public function moodTags(): HasMany
    {
        return $this->hasMany(PostMoodTag::class);
    }
}
