<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Health resource model.
 *
 * NOTE: In controllers and anywhere you need to import this model alongside
 * Laravel's built-in JsonResource, alias it like so:
 *   use App\Models\Resource as HealthResource;
 */
class Resource extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'thumbnail',
        'duration_meta',
        'file_path',
        'file_type',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function body(): HasOne
    {
        return $this->hasOne(ResourceBody::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(ResourceTag::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
