<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfessionalTitle extends Model
{
    protected $fillable = [
        'name',
    ];

    public function doctorApplicationProfessionalTitles(): HasMany
    {
        return $this->hasMany(DoctorApplicationProfessionalTitle::class);
    }
}
