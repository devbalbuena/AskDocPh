<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorRequirement extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
        ];
    }

    public function applicationDocuments(): HasMany
    {
        return $this->hasMany(DoctorApplicationDocument::class);
    }
}
