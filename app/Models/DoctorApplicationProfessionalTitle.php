<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorApplicationProfessionalTitle extends Model
{
    public $timestamps = false;

    protected $table = 'doctor_application_professional_titles';

    protected $fillable = [
        'doctor_application_id',
        'professional_title_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(DoctorApplication::class, 'doctor_application_id');
    }

    public function professionalTitle(): BelongsTo
    {
        return $this->belongsTo(ProfessionalTitle::class);
    }
}
