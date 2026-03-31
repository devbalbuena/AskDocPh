<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResourceTagType extends Model
{
    protected $fillable = [
        'name',
    ];

    public function resourceTags(): HasMany
    {
        return $this->hasMany(ResourceTag::class, 'tag_type_id');
    }
}
