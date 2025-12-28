<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Office;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // SUPERADMIN
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@mesa.gob.pe'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole('superadmin');

        // ADMIN
        $admin = User::firstOrCreate(
            ['email' => 'admin@mesa.gob.pe'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // JEFE MESA DE PARTES
        $jefe = User::firstOrCreate(
            ['email' => 'jefe.mp@mesa.gob.pe'],
            [
                'name' => 'Jefe Mesa de Partes',
                'password' => Hash::make('password'),
            ]
        );
        $jefe->assignRole('jefe');

        $mesaPartes = Office::where('acronym', 'MP')->first();
        $jefe->offices()->syncWithoutDetaching([
            $mesaPartes->id => [
                'is_boss' => true,
                'assigned_at' => now(),
            ],
        ]);

        // SECRETARIA
        $secretaria = User::firstOrCreate(
            ['email' => 'secretaria.mp@mesa.gob.pe'],
            [
                'name' => 'Secretaria Mesa de Partes',
                'password' => Hash::make('password'),
            ]
        );
        $secretaria->assignRole('secretaria');
        $secretaria->offices()->syncWithoutDetaching([
            $mesaPartes->id => [
                'is_boss' => false,
                'assigned_at' => now(),
            ],
        ]);
    }
}
