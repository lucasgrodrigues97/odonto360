<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dentist>
 */
class DentistFactory extends Factory
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
            'crm' => fake()->unique()->numerify('######'),
            'specialization' => fake()->randomElement([
                'Odontologia Geral', 'Ortodontia', 'Endodontia', 'Periodontia',
                'Implantodontia', 'Odontopediatria', 'Prótese Dentária',
            ]),
            'experience_years' => fake()->numberBetween(1, 30),
            'consultation_duration' => fake()->randomElement([30, 45, 60, 90, 120]),
            'consultation_price' => fake()->randomFloat(2, 100, 500),
            'bio' => fake()->paragraph(),
            'is_active' => true,
            'available_days' => fake()->randomElements([1, 2, 3, 4, 5, 6, 7], fake()->numberBetween(3, 7)),
            'available_hours_start' => fake()->time('H:i', '08:00'),
            'available_hours_end' => fake()->time('H:i', '18:00'),
        ];
    }
}
