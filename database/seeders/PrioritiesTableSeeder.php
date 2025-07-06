<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Priority; // Importa el modelo Priority

class PrioritiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Ejecuta los seeders de la base de datos para crear los niveles de prioridad.
     */
    public function run(): void
    {
        // Niveles de prioridad a crear.
        $priorities = [
            'Alta',
            'Media',
            'Baja',
        ];

        foreach ($priorities as $levelName) {
            // Crea el nivel de prioridad si no existe.
            Priority::firstOrCreate(['level' => $levelName]);
            $this->command->info("Nivel de prioridad '{$levelName}' asegurado/creado.");
        }

        $this->command->info('Niveles de prioridad creados exitosamente o ya existentes.');
    }
}
