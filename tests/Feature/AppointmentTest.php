<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users and their related models
        $this->patientUser = User::factory()->create();
        $this->patient = Patient::factory()->create(['user_id' => $this->patientUser->id]);

        $this->dentistUser = User::factory()->create();
        $this->dentist = Dentist::factory()->create(['user_id' => $this->dentistUser->id]);
    }

    public function test_patient_can_create_appointment()
    {
        $this->patientUser->assignRole('patient');

        $appointmentData = [
            'dentist_id' => $this->dentist->id,
            'appointment_date' => now()->addDays(1)->format('Y-m-d'),
            'appointment_time' => '10:00',
            'duration' => 60,
            'notes' => 'Consulta de rotina',
        ];

        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/appointments', $appointmentData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'patient_id',
                    'dentist_id',
                    'appointment_date',
                    'appointment_time',
                    'status',
                ],
            ]);

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $this->patient->id,
            'dentist_id' => $this->dentist->id,
            'appointment_date' => $appointmentData['appointment_date'],
        ]);
    }

    public function test_patient_can_view_their_appointments()
    {
        $this->patientUser->assignRole('patient');

        $appointment = Appointment::factory()->create([
            'patient_id' => $this->patient->id,
            'dentist_id' => $this->dentist->id,
        ]);

        $response = $this->actingAs($this->patientUser)
            ->getJson('/api/appointments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'patient_id',
                            'dentist_id',
                            'appointment_date',
                            'appointment_time',
                            'status',
                        ],
                    ],
                ],
            ]);
    }

    public function test_dentist_can_view_their_appointments()
    {
        $this->dentistUser->assignRole('dentist');

        $appointment = Appointment::factory()->create([
            'patient_id' => $this->patient->id,
            'dentist_id' => $this->dentist->id,
        ]);

        $response = $this->actingAs($this->dentistUser)
            ->getJson('/api/dentists/profile/appointments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'patient_id',
                            'dentist_id',
                            'appointment_date',
                            'appointment_time',
                            'status',
                        ],
                    ],
                ],
            ]);
    }

    public function test_dentist_can_update_appointment_status()
    {
        $this->dentistUser->assignRole('dentist');

        $appointment = Appointment::factory()->create([
            'patient_id' => $this->patient->id,
            'dentist_id' => $this->dentist->id,
            'status' => 'scheduled',
        ]);

        $updateData = [
            'status' => 'confirmed',
            'notes' => 'Consulta confirmada',
        ];

        $response = $this->actingAs($this->dentistUser)
            ->putJson("/api/appointments/{$appointment->id}/status", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Status do agendamento atualizado com sucesso',
            ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_patient_can_cancel_appointment()
    {
        $this->patientUser->assignRole('patient');

        $appointment = Appointment::factory()->create([
            'patient_id' => $this->patient->id,
            'dentist_id' => $this->dentist->id,
            'status' => 'scheduled',
        ]);

        $cancelData = [
            'cancellation_reason' => 'Mudança de planos',
        ];

        $response = $this->actingAs($this->patientUser)
            ->postJson("/api/appointments/{$appointment->id}/cancel", $cancelData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Agendamento cancelado com sucesso',
            ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_cannot_create_appointment_with_invalid_data()
    {
        $this->patientUser->assignRole('patient');

        $appointmentData = [
            'dentist_id' => 999, // Non-existent dentist
            'appointment_date' => 'invalid-date',
            'appointment_time' => 'invalid-time',
        ];

        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/appointments', $appointmentData);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
            ]);
    }

    public function test_cannot_create_appointment_in_past()
    {
        $this->patientUser->assignRole('patient');

        $appointmentData = [
            'dentist_id' => $this->dentist->id,
            'appointment_date' => now()->subDays(1)->format('Y-m-d'),
            'appointment_time' => '10:00',
        ];

        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/appointments', $appointmentData);

        $response->assertStatus(422);
    }
}
