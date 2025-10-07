<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $appointmentDate = fake()->dateTimeBetween('now', '+3 months');
        $appointmentTime = fake()->time('H:i', '17:00');
        
        return [
            'patient_id' => Patient::factory(),
            'dentist_id' => Dentist::factory(),
            'appointment_date' => $appointmentDate->format('Y-m-d'),
            'appointment_time' => $appointmentTime,
            'duration' => fake()->randomElement([30, 45, 60, 90, 120]),
            'status' => fake()->randomElement([
                Appointment::STATUS_SCHEDULED,
                Appointment::STATUS_CONFIRMED,
                Appointment::STATUS_IN_PROGRESS,
                Appointment::STATUS_COMPLETED,
                Appointment::STATUS_CANCELLED,
                Appointment::STATUS_NO_SHOW,
            ]),
            'notes' => fake()->optional()->sentence(),
            'reason' => fake()->optional()->sentence(),
            'treatment_plan' => fake()->optional()->paragraph(),
            'cost' => fake()->randomFloat(2, 100, 1000),
            'payment_status' => fake()->randomElement([
                Appointment::PAYMENT_PENDING,
                Appointment::PAYMENT_PAID,
                Appointment::PAYMENT_PARTIAL,
                Appointment::PAYMENT_REFUNDED,
            ]),
            'reminder_sent' => fake()->boolean(),
            'cancellation_reason' => fake()->optional()->sentence(),
            'cancelled_at' => fake()->optional()->dateTime(),
        ];
    }

    /**
     * Indicate that the appointment is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Appointment::STATUS_SCHEDULED,
            'cancelled_at' => null,
        ]);
    }

    /**
     * Indicate that the appointment is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Appointment::STATUS_CONFIRMED,
            'cancelled_at' => null,
        ]);
    }

    /**
     * Indicate that the appointment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Appointment::STATUS_COMPLETED,
            'cancelled_at' => null,
        ]);
    }

    /**
     * Indicate that the appointment is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Appointment::STATUS_CANCELLED,
            'cancelled_at' => fake()->dateTime(),
            'cancellation_reason' => fake()->sentence(),
        ]);
    }
}
