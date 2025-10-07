<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\User;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = [
            [
                'user_id' => 3, // Carlos Oliveira
                'patient_code' => 'PAT001',
                'emergency_contact_name' => 'Maria Oliveira',
                'emergency_contact_phone' => '(11) 91111-1111',
                'medical_conditions' => ['Hipertensão', 'Diabetes tipo 2'],
                'allergies' => ['Penicilina'],
                'medications' => ['Losartana 50mg', 'Metformina 850mg'],
                'insurance_provider' => 'Unimed',
                'insurance_number' => '123456789',
                'notes' => 'Paciente com histórico de ansiedade durante procedimentos.',
                'is_active' => true,
            ],
            [
                'user_id' => 4, // Ana Costa
                'patient_code' => 'PAT002',
                'emergency_contact_name' => 'Roberto Costa',
                'emergency_contact_phone' => '(11) 92222-2222',
                'medical_conditions' => [],
                'allergies' => [],
                'medications' => [],
                'insurance_provider' => 'Bradesco Saúde',
                'insurance_number' => '987654321',
                'notes' => 'Paciente regular, sem complicações.',
                'is_active' => true,
            ],
            [
                'user_id' => 5, // Pedro Ferreira
                'patient_code' => 'PAT003',
                'emergency_contact_name' => 'Lucia Ferreira',
                'emergency_contact_phone' => '(11) 93333-3333',
                'medical_conditions' => ['Asma'],
                'allergies' => ['Látex'],
                'medications' => ['Salbutamol'],
                'insurance_provider' => 'SulAmérica',
                'insurance_number' => '456789123',
                'notes' => 'Cuidado com materiais que contenham látex.',
                'is_active' => true,
            ],
        ];

        foreach ($patients as $patient) {
            Patient::create($patient);
        }
    }
}
