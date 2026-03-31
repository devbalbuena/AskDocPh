<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorApplicationDocument extends Model
{
    protected $fillable = [
        'doctor_application_id',
        'doctor_requirement_id',
        'document_type',
        'file_path',
        'text_value',
        'status',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(DoctorApplication::class, 'doctor_application_id');
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(DoctorRequirement::class, 'doctor_requirement_id');
    }
}
