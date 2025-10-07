<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specialization;

class SpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specializations = [
            [
                'name' => 'Odontologia Geral',
                'description' => 'Atendimento geral e preventivo em odontologia',
                'is_active' => true,
            ],
            [
                'name' => 'Ortodontia',
                'description' => 'Correção de posicionamento dos dentes e maxilares',
                'is_active' => true,
            ],
            [
                'name' => 'Endodontia',
                'description' => 'Tratamento de canal e problemas da polpa dentária',
                'is_active' => true,
            ],
            [
                'name' => 'Periodontia',
                'description' => 'Tratamento das gengivas e estruturas de suporte dos dentes',
                'is_active' => true,
            ],
            [
                'name' => 'Implantodontia',
                'description' => 'Colocação de implantes dentários',
                'is_active' => true,
            ],
            [
                'name' => 'Odontopediatria',
                'description' => 'Atendimento odontológico para crianças',
                'is_active' => true,
            ],
            [
                'name' => 'Prótese Dentária',
                'description' => 'Reabilitação oral com próteses fixas e removíveis',
                'is_active' => true,
            ],
            [
                'name' => 'Cirurgia Oral',
                'description' => 'Procedimentos cirúrgicos na cavidade oral',
                'is_active' => true,
            ],
            [
                'name' => 'Estética Dental',
                'description' => 'Procedimentos estéticos e cosméticos',
                'is_active' => true,
            ],
            [
                'name' => 'Odontologia do Trabalho',
                'description' => 'Saúde bucal no ambiente de trabalho',
                'is_active' => true,
            ],
        ];

        foreach ($specializations as $specialization) {
            Specialization::create($specialization);
        }
    }
}
