<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MoodTag extends Model
{
    protected $fillable = [
        'name',
        'color',
    ];

    public function postMoodTags(): HasMany
    {
        return $this->hasMany(PostMoodTag::class);
    }
}
