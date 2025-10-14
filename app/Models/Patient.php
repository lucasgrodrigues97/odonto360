<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'patient_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'medical_conditions',
        'allergies',
        'medications',
        'insurance_provider',
        'insurance_number',
        'notes',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'medical_conditions' => 'array',
        'allergies' => 'array',
        'medications' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the patient.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the appointments for the patient.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the patient's medical history.
     */
    public function medicalHistory()
    {
        return $this->hasMany(MedicalHistory::class);
    }

    /**
     * Get the patient's treatments.
     */
    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    /**
     * Scope a query to only include active patients.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the patient's full name.
     */
    public function getFullNameAttribute()
    {
        return $this->user->name;
    }

    /**
     * Get the patient's email.
     */
    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    /**
     * Get the patient's phone.
     */
    public function getPhoneAttribute()
    {
        return $this->user->phone;
    }
}
