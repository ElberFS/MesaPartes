<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Office;

class OfficeSeeder extends Seeder
{
    public function run(): void
    {
        $offices = [
            ['name' => 'Mesa de Partes', 'acronym' => 'MP'],
            ['name' => 'Rectorado', 'acronym' => 'RECT'],
            ['name' => 'Oficina de Tecnologías de la Información', 'acronym' => 'OTI'],
            ['name' => 'Recursos Humanos', 'acronym' => 'RRHH'],
        ];

        foreach ($offices as $office) {
            Office::firstOrCreate(
                ['acronym' => $office['acronym']],
                [
                    'name' => $office['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
