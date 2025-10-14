<?php

namespace Database\Seeders;

use App\Models\Procedure;
use Illuminate\Database\Seeder;

class ProcedureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $procedures = [
            // Consultas e Exames
            [
                'name' => 'Consulta de Avaliação',
                'description' => 'Consulta inicial para avaliação do paciente',
                'code' => 'CONS001',
                'price' => 150.00,
                'duration' => 60,
                'category' => 'Consulta',
                'is_active' => true,
            ],
            [
                'name' => 'Consulta de Retorno',
                'description' => 'Consulta de acompanhamento',
                'code' => 'CONS002',
                'price' => 100.00,
                'duration' => 30,
                'category' => 'Consulta',
                'is_active' => true,
            ],
            [
                'name' => 'Exame Radiográfico Panorâmico',
                'description' => 'Radiografia panorâmica dos maxilares',
                'code' => 'RAD001',
                'price' => 80.00,
                'duration' => 15,
                'category' => 'Exame',
                'is_active' => true,
            ],
            [
                'name' => 'Exame Radiográfico Periapical',
                'description' => 'Radiografia periapical de dente específico',
                'code' => 'RAD002',
                'price' => 25.00,
                'duration' => 10,
                'category' => 'Exame',
                'is_active' => true,
            ],

            // Limpeza e Prevenção
            [
                'name' => 'Profilaxia (Limpeza)',
                'description' => 'Limpeza completa dos dentes',
                'code' => 'LIM001',
                'price' => 120.00,
                'duration' => 60,
                'category' => 'Prevenção',
                'is_active' => true,
            ],
            [
                'name' => 'Aplicação de Flúor',
                'description' => 'Aplicação tópica de flúor',
                'code' => 'FLU001',
                'price' => 50.00,
                'duration' => 20,
                'category' => 'Prevenção',
                'is_active' => true,
            ],

            // Restaurações
            [
                'name' => 'Restauração em Resina',
                'description' => 'Restauração estética em resina composta',
                'code' => 'RES001',
                'price' => 200.00,
                'duration' => 90,
                'category' => 'Restauração',
                'is_active' => true,
            ],
            [
                'name' => 'Restauração em Amálgama',
                'description' => 'Restauração em amálgama de prata',
                'code' => 'RES002',
                'price' => 150.00,
                'duration' => 60,
                'category' => 'Restauração',
                'is_active' => true,
            ],

            // Endodontia
            [
                'name' => 'Tratamento de Canal',
                'description' => 'Tratamento endodôntico completo',
                'code' => 'CAN001',
                'price' => 800.00,
                'duration' => 120,
                'category' => 'Endodontia',
                'is_active' => true,
            ],
            [
                'name' => 'Retratamento de Canal',
                'description' => 'Retratamento endodôntico',
                'code' => 'CAN002',
                'price' => 1000.00,
                'duration' => 150,
                'category' => 'Endodontia',
                'is_active' => true,
            ],

            // Cirurgia
            [
                'name' => 'Extração Simples',
                'description' => 'Extração de dente sem complicações',
                'code' => 'EXT001',
                'price' => 200.00,
                'duration' => 60,
                'category' => 'Cirurgia',
                'is_active' => true,
            ],
            [
                'name' => 'Extração Cirúrgica',
                'description' => 'Extração com procedimento cirúrgico',
                'code' => 'EXT002',
                'price' => 400.00,
                'duration' => 90,
                'category' => 'Cirurgia',
                'is_active' => true,
            ],

            // Próteses
            [
                'name' => 'Coroa de Porcelana',
                'description' => 'Coroa estética em porcelana',
                'code' => 'COR001',
                'price' => 1200.00,
                'duration' => 180,
                'category' => 'Prótese',
                'is_active' => true,
            ],
            [
                'name' => 'Prótese Parcial Removível',
                'description' => 'Prótese parcial removível',
                'code' => 'PRO001',
                'price' => 800.00,
                'duration' => 240,
                'category' => 'Prótese',
                'is_active' => true,
            ],

            // Ortodontia
            [
                'name' => 'Avaliação Ortodôntica',
                'description' => 'Avaliação para tratamento ortodôntico',
                'code' => 'ORT001',
                'price' => 300.00,
                'duration' => 90,
                'category' => 'Ortodontia',
                'is_active' => true,
            ],
            [
                'name' => 'Manutenção de Aparelho',
                'description' => 'Ajuste e manutenção do aparelho ortodôntico',
                'code' => 'ORT002',
                'price' => 150.00,
                'duration' => 45,
                'category' => 'Ortodontia',
                'is_active' => true,
            ],
        ];

        foreach ($procedures as $procedure) {
            Procedure::create($procedure);
        }
    }
}
