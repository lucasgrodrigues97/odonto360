<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Patient;
use App\Models\Procedure;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = Patient::all();
        $dentists = Dentist::all();
        $procedures = Procedure::all();

        if ($patients->isEmpty() || $dentists->isEmpty() || $procedures->isEmpty()) {
            $this->command->warn('Patients, Dentists, or Procedures not found. Skipping appointments seeding.');

            return;
        }

        // Criar mais agendamentos para popular os gráficos
        $appointments = [];

        // Agendamentos dos últimos 30 dias com diferentes status
        for ($i = 0; $i < 50; $i++) {
            $appointments[] = [
                'patient_id' => $patients->random()->id,
                'dentist_id' => $dentists->random()->id,
                'appointment_date' => Carbon::today()->subDays(rand(0, 30)),
                'appointment_time' => sprintf('%02d:%02d:00', rand(8, 18), rand(0, 59)),
                'duration' => rand(30, 120),
                'status' => ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'][rand(0, 4)],
                'reason' => 'Consulta gerada automaticamente '.($i + 1),
            ];
        }

        // Adicionar alguns agendamentos de hoje para garantir dados
        for ($i = 0; $i < 5; $i++) {
            $appointments[] = [
                'patient_id' => $patients->random()->id,
                'dentist_id' => $dentists->random()->id,
                'appointment_date' => Carbon::today(),
                'appointment_time' => sprintf('%02d:%02d:00', rand(8, 18), rand(0, 59)),
                'duration' => rand(30, 120),
                'status' => ['scheduled', 'confirmed', 'completed'][rand(0, 2)],
                'reason' => 'Consulta de hoje '.($i + 1),
            ];
        }

        // Adicionar alguns agendamentos específicos
        $appointments = array_merge($appointments, [
            [
                'patient_id' => $patients->first()->id,
                'dentist_id' => $dentists->first()->id,
                'appointment_date' => Carbon::today()->addDays(1),
                'appointment_time' => '09:00:00',
                'duration' => 60,
                'status' => 'scheduled',
                'reason' => 'Consulta de rotina',
            ],
            [
                'patient_id' => $patients->skip(1)->first()?->id ?? $patients->first()->id,
                'dentist_id' => $dentists->first()->id,
                'appointment_date' => Carbon::today()->addDays(2),
                'appointment_time' => '14:00:00',
                'duration' => 90,
                'status' => 'confirmed',
                'reason' => 'Limpeza dental',
            ],
            [
                'patient_id' => $patients->first()->id,
                'dentist_id' => $dentists->skip(1)->first()?->id ?? $dentists->first()->id,
                'appointment_date' => Carbon::today()->addDays(3),
                'appointment_time' => '10:30:00',
                'duration' => 120,
                'status' => 'scheduled',
                'reason' => 'Restauração',
            ],
            [
                'patient_id' => $patients->skip(1)->first()?->id ?? $patients->first()->id,
                'dentist_id' => $dentists->first()->id,
                'appointment_date' => Carbon::today(),
                'appointment_time' => '15:00:00',
                'duration' => 60,
                'status' => 'completed',
                'reason' => 'Consulta de emergência',
            ],
            [
                'patient_id' => $patients->first()->id,
                'dentist_id' => $dentists->first()->id,
                'appointment_date' => Carbon::today()->subDays(1),
                'appointment_time' => '11:00:00',
                'duration' => 90,
                'status' => 'completed',
                'reason' => 'Extração de dente',
            ],
        ]);

        foreach ($appointments as $appointmentData) {
            $appointment = Appointment::create($appointmentData);

            // Attach random procedures to appointments with prices
            $randomProcedures = $procedures->random(rand(1, 3));
            $procedureData = [];
            foreach ($randomProcedures as $procedure) {
                $procedureData[$procedure->id] = [
                    'price' => $procedure->price,
                    'quantity' => 1,
                    'notes' => null,
                ];
            }
            $appointment->procedures()->attach($procedureData);
        }

        $this->command->info('Appointments seeded successfully!');
    }
}
