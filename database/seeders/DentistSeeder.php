<?php

namespace Database\Seeders;

use App\Models\Dentist;
use Illuminate\Database\Seeder;

class DentistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dentists = [
            [
                'user_id' => 2, // Dr. João Silva
                'crm' => '12345',
                'specialization' => 'Odontologia Geral',
                'experience_years' => 15,
                'consultation_duration' => 60,
                'consultation_price' => 200.00,
                'bio' => 'Especialista em odontologia geral com 15 anos de experiência. Focado em tratamentos preventivos e restauradores.',
                'is_active' => true,
                'available_days' => [1, 2, 3, 4, 5], // Segunda a sexta
                'available_hours_start' => '08:00',
                'available_hours_end' => '18:00',
            ],
            [
                'user_id' => 3, // Dra. Maria Santos
                'crm' => '67890',
                'specialization' => 'Ortodontia',
                'experience_years' => 10,
                'consultation_duration' => 90,
                'consultation_price' => 300.00,
                'bio' => 'Especialista em ortodontia com 10 anos de experiência. Atende pacientes de todas as idades.',
                'is_active' => true,
                'available_days' => [1, 2, 3, 4, 5], // Segunda a sexta
                'available_hours_start' => '09:00',
                'available_hours_end' => '17:00',
            ],
        ];

        foreach ($dentists as $dentist) {
            $createdDentist = Dentist::create($dentist);

            // Assign specializations
            if ($dentist['user_id'] == 2) { // Dr. João Silva
                $createdDentist->specializations()->attach([1, 3, 4]); // Odontologia Geral, Endodontia, Periodontia
            } elseif ($dentist['user_id'] == 3) { // Dra. Maria Santos
                $createdDentist->specializations()->attach([2, 6]); // Ortodontia, Odontopediatria
            }
        }
    }
}
