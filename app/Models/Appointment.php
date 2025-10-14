<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
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
        'appointment_date',
        'appointment_time',
        'duration',
        'status',
        'notes',
        'reason',
        'treatment_plan',
        'cost',
        'payment_status',
        'reminder_sent',
        'cancellation_reason',
        'cancelled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i',
        'duration' => 'integer',
        'cost' => 'decimal:2',
        'reminder_sent' => 'boolean',
        'cancelled_at' => 'datetime',
    ];

    /**
     * The possible status values for appointments.
     */
    const STATUS_SCHEDULED = 'scheduled';

    const STATUS_CONFIRMED = 'confirmed';

    const STATUS_IN_PROGRESS = 'in_progress';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_NO_SHOW = 'no_show';

    /**
     * The possible payment status values.
     */
    const PAYMENT_PENDING = 'pending';

    const PAYMENT_PAID = 'paid';

    const PAYMENT_PARTIAL = 'partial';

    const PAYMENT_REFUNDED = 'refunded';

    /**
     * Get the patient that owns the appointment.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the dentist that owns the appointment.
     */
    public function dentist()
    {
        return $this->belongsTo(Dentist::class);
    }

    /**
     * Get the treatments for the appointment.
     */
    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    /**
     * Get the appointment's procedures.
     */
    public function procedures()
    {
        return $this->belongsToMany(Procedure::class, 'appointment_procedures')
            ->withPivot('quantity', 'price', 'notes')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include appointments for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('appointment_date', $date);
    }

    /**
     * Scope a query to only include appointments for a specific dentist.
     */
    public function scopeForDentist($query, $dentistId)
    {
        return $query->where('dentist_id', $dentistId);
    }

    /**
     * Scope a query to only include appointments for a specific patient.
     */
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /**
     * Scope a query to only include appointments with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include upcoming appointments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', [self::STATUS_SCHEDULED, self::STATUS_CONFIRMED]);
    }

    /**
     * Get the appointment's formatted date and time.
     */
    public function getFormattedDateTimeAttribute()
    {
        return $this->appointment_date->format('d/m/Y').' às '.$this->appointment_time->format('H:i');
    }

    /**
     * Get the appointment's status in Portuguese.
     */
    public function getStatusInPortugueseAttribute()
    {
        $statuses = [
            self::STATUS_SCHEDULED => 'Agendado',
            self::STATUS_CONFIRMED => 'Confirmado',
            self::STATUS_IN_PROGRESS => 'Em Andamento',
            self::STATUS_COMPLETED => 'Concluído',
            self::STATUS_CANCELLED => 'Cancelado',
            self::STATUS_NO_SHOW => 'Não Compareceu',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Check if the appointment can be cancelled.
     */
    public function canBeCancelled()
    {
        return in_array($this->status, [self::STATUS_SCHEDULED, self::STATUS_CONFIRMED]) &&
               $this->appointment_date->isFuture();
    }

    /**
     * Check if the appointment can be rescheduled.
     */
    public function canBeRescheduled()
    {
        return in_array($this->status, [self::STATUS_SCHEDULED, self::STATUS_CONFIRMED]) &&
               $this->appointment_date->isFuture();
    }
}
