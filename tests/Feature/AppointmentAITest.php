<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Dentist;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Procedure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AppointmentAITest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        $this->admin->assignRole('admin');
        
        $this->dentist = User::factory()->create(['email' => 'dentist@test.com']);
        $this->dentist->assignRole('dentist');
        
        $this->patient = User::factory()->create(['email' => 'patient@test.com']);
        $this->patient->assignRole('patient');
        
        // Create dentist profile
        $this->dentistProfile = Dentist::create([
            'user_id' => $this->dentist->id,
            'crm' => '12345',
            'specialization' => 'Ortodontia',
            'consultation_fee' => 150.00,
        ]);
        
        // Create patient profile
        $this->patientProfile = Patient::create([
            'user_id' => $this->patient->id,
            'cpf' => '12345678901',
            'birth_date' => '1990-01-01',
            'address' => 'Rua Teste, 123',
        ]);
        
        // Create procedures
        $this->procedure = Procedure::create([
            'name' => 'Limpeza',
            'description' => 'Limpeza dental',
            'duration' => 60,
            'price' => 100.00,
            'category' => 'Preventivo',
        ]);
    }

    public function test_ai_suggestions_require_authentication()
    {
        $response = $this->getJson('/api/appointments/ai-suggestions');
        
        $response->assertStatus(401);
    }

    public function test_ai_suggestions_work_for_authenticated_users()
    {
        $response = $this->actingAs($this->patient, 'sanctum')
            ->getJson('/api/appointments/ai-suggestions');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'suggestions',
                    'confidence',
                    'reasoning'
                ]
            ]);
    }

    public function test_ai_analysis_requires_dentist_id()
    {
        $response = $this->actingAs($this->patient, 'sanctum')
            ->getJson('/api/appointments/ai-analysis/999');
        
        $response->assertStatus(404);
    }

    public function test_ai_analysis_works_with_valid_dentist()
    {
        $response = $this->actingAs($this->patient, 'sanctum')
            ->getJson('/api/appointments/ai-analysis/' . $this->dentistProfile->id);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'patterns',
                    'recommendations',
                    'insights'
                ]
            ]);
    }

    public function test_ai_predictions_require_date()
    {
        $response = $this->actingAs($this->patient, 'sanctum')
            ->getJson('/api/appointments/ai-predictions/' . $this->dentistProfile->id);
        
        $response->assertStatus(422);
    }

    public function test_ai_predictions_work_with_valid_data()
    {
        $response = $this->actingAs($this->patient, 'sanctum')
            ->getJson('/api/appointments/ai-predictions/' . $this->dentistProfile->id . '?date=' . now()->addDays(7)->format('Y-m-d'));
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'optimal_times',
                    'confidence_scores',
                    'reasoning'
                ]
            ]);
    }

    public function test_ai_suggestions_consider_existing_appointments()
    {
        // Create existing appointment
        Appointment::create([
            'patient_id' => $this->patientProfile->id,
            'dentist_id' => $this->dentistProfile->id,
            'appointment_date' => now()->addDays(1)->format('Y-m-d'),
            'appointment_time' => '14:00:00',
            'duration' => 60,
            'status' => 'scheduled',
            'reason' => 'Consulta de rotina',
        ]);

        $response = $this->actingAs($this->patient, 'sanctum')
            ->getJson('/api/appointments/ai-suggestions?dentist_id=' . $this->dentistProfile->id);
        
        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertNotEmpty($data['suggestions']);
    }
}
