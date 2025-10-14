<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name' => 'Administrador do Sistema',
            'email' => 'admin@odonto360.com',
            'password' => Hash::make('password'),
            'phone' => '(11) 99999-9999',
            'cpf' => '12345678901',
            'birth_date' => '1980-01-01',
            'gender' => 'male',
            'address' => 'Rua das Flores, 123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01234-567',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Dentist users
        $dentist1 = User::create([
            'name' => 'Dr. João Silva',
            'email' => 'joao.silva@odonto360.com',
            'password' => Hash::make('password'),
            'phone' => '(11) 98888-8888',
            'cpf' => '11111111111',
            'birth_date' => '1985-05-15',
            'gender' => 'male',
            'address' => 'Av. Paulista, 1000',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01310-100',
            'is_active' => true,
        ]);
        $dentist1->assignRole('dentist');

        $dentist2 = User::create([
            'name' => 'Dra. Maria Santos',
            'email' => 'maria.santos@odonto360.com',
            'password' => Hash::make('password'),
            'phone' => '(11) 97777-7777',
            'cpf' => '22222222222',
            'birth_date' => '1990-08-20',
            'gender' => 'female',
            'address' => 'Rua Augusta, 500',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01305-000',
            'is_active' => true,
        ]);
        $dentist2->assignRole('dentist');

        // Patient users
        $patient1 = User::create([
            'name' => 'Carlos Oliveira',
            'email' => 'carlos.oliveira@email.com',
            'password' => Hash::make('password'),
            'phone' => '(11) 96666-6666',
            'cpf' => '33333333333',
            'birth_date' => '1992-03-10',
            'gender' => 'male',
            'address' => 'Rua da Consolação, 200',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01302-000',
            'is_active' => true,
        ]);
        $patient1->assignRole('patient');

        $patient2 = User::create([
            'name' => 'Ana Costa',
            'email' => 'ana.costa@email.com',
            'password' => Hash::make('password'),
            'phone' => '(11) 95555-5555',
            'cpf' => '44444444444',
            'birth_date' => '1988-12-05',
            'gender' => 'female',
            'address' => 'Av. Faria Lima, 1500',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01451-000',
            'is_active' => true,
        ]);
        $patient2->assignRole('patient');

        $patient3 = User::create([
            'name' => 'Pedro Ferreira',
            'email' => 'pedro.ferreira@email.com',
            'password' => Hash::make('password'),
            'phone' => '(11) 94444-4444',
            'cpf' => '55555555555',
            'birth_date' => '1995-07-18',
            'gender' => 'male',
            'address' => 'Rua Oscar Freire, 800',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01426-000',
            'is_active' => true,
        ]);
        $patient3->assignRole('patient');
    }
}
