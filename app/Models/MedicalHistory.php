<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalHistory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'dentist_id',
        'appointment_id',
        'date',
        'description',
        'diagnosis',
        'treatment',
        'notes',
        'attachments',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'attachments' => 'array',
    ];

    /**
     * Get the patient that owns the medical history.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the dentist that created the medical history.
     */
    public function dentist()
    {
        return $this->belongsTo(Dentist::class);
    }

    /**
     * Get the appointment associated with the medical history.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
