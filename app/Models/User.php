<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'cpf',
        'birth_date',
        'gender',
        'address',
        'city',
        'state',
        'zip_code',
        'google_id',
        'avatar',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the patient record associated with the user.
     */
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    /**
     * Get the dentist record associated with the user.
     */
    public function dentist()
    {
        return $this->hasOne(Dentist::class);
    }

    /**
     * Get the appointments for this user.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Check if user is a patient.
     */
    public function isPatient()
    {
        return $this->hasRole('patient');
    }

    /**
     * Check if user is a dentist.
     */
    public function isDentist()
    {
        return $this->hasRole('dentist');
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }
}
