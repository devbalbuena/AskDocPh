<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_comment_id',
        'comment_text',
    ];

    // ─── Relationships ────────────────────────────────

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Self-referencing: the parent comment this reply belongs to.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(PostComment::class, 'parent_comment_id');
    }

    /**
     * Self-referencing: all direct child replies to this comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(PostComment::class, 'parent_comment_id');
    }
}
