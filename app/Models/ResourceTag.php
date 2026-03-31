<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceTag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'resource_id',
        'tag_type_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function tagType(): BelongsTo
    {
        return $this->belongsTo(ResourceTagType::class, 'tag_type_id');
    }
}
