<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'patient_code' => 'PAT'.fake()->unique()->numberBetween(1000, 9999),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'medical_conditions' => fake()->randomElements([
                'Hipertensão', 'Diabetes', 'Asma', 'Alergia a penicilina', 'Problemas cardíacos',
            ], fake()->numberBetween(0, 3)),
            'allergies' => fake()->randomElements([
                'Penicilina', 'Látex', 'Amoxicilina', 'Ibuprofeno', 'Aspirina',
            ], fake()->numberBetween(0, 2)),
            'medications' => fake()->randomElements([
                'Losartana 50mg', 'Metformina 850mg', 'Salbutamol', 'Atenolol 25mg',
            ], fake()->numberBetween(0, 2)),
            'insurance_provider' => fake()->randomElement(['Unimed', 'Bradesco Saúde', 'SulAmérica', 'Amil']),
            'insurance_number' => fake()->numerify('##########'),
            'notes' => fake()->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
