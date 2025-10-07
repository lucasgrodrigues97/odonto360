<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dentist extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'crm',
        'specialization',
        'experience_years',
        'consultation_duration',
        'consultation_price',
        'bio',
        'is_active',
        'available_days',
        'available_hours_start',
        'available_hours_end',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'available_days' => 'array',
        'is_active' => 'boolean',
        'consultation_duration' => 'integer',
        'consultation_price' => 'decimal:2',
    ];

    /**
     * Get the user that owns the dentist.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the appointments for the dentist.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the dentist's specializations.
     */
    public function specializations()
    {
        return $this->belongsToMany(Specialization::class, 'dentist_specializations');
    }

    /**
     * Get the dentist's schedules.
     */
    public function schedules()
    {
        return $this->hasMany(DentistSchedule::class);
    }

    /**
     * Scope a query to only include active dentists.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the dentist's full name.
     */
    public function getFullNameAttribute()
    {
        return $this->user->name;
    }

    /**
     * Get the dentist's email.
     */
    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    /**
     * Get the dentist's phone.
     */
    public function getPhoneAttribute()
    {
        return $this->user->phone;
    }

    /**
     * Check if dentist is available on a specific date and time.
     */
    public function isAvailable($date, $time)
    {
        $dayOfWeek = date('N', strtotime($date));
        
        // Check if dentist works on this day
        if (!in_array($dayOfWeek, $this->available_days ?? [])) {
            return false;
        }

        // Check if time is within working hours
        $time = date('H:i', strtotime($time));
        if ($time < $this->available_hours_start || $time > $this->available_hours_end) {
            return false;
        }

        // Check if there's already an appointment at this time
        $existingAppointment = $this->appointments()
            ->where('appointment_date', $date)
            ->where('appointment_time', $time)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->exists();

        return !$existingAppointment;
    }
}
