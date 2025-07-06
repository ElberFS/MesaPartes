<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Office;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Ejecuta los seeders de la base de datos para crear usuarios y asignarles roles.
     */
    public function run(): void
    {
        // Asegúrate de que los roles existan antes de intentar asignarlos.
        // Puedes ejecutar el seeder de roles primero si no lo has hecho.
        $this->call(RolesTableSeeder::class);

        // Crea algunas oficinas de ejemplo si no existen
        $office1 = Office::firstOrCreate(['name' => 'Oficina Principal']);
        $office2 = Office::firstOrCreate(['name' => 'Oficina de Recursos Humanos']);
        $office3 = Office::firstOrCreate(['name' => 'Oficina de Contabilidad']);

        // Crea un usuario para el rol 'administrador'
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador General',
                'password' => Hash::make('password'),
                'office_id' => $office1->id,
                'email_verified_at' => now(),
            ]
        );
        $adminRole = Role::where('name', 'administrador')->first();
        if ($adminRole) {
            $adminUser->assignRole($adminRole);
            $this->command->info("Usuario '{$adminUser->name}' creado y asignado al rol 'administrador'.");
        } else {
            $this->command->warn("Rol 'administrador' no encontrado. Asegúrate de ejecutar RolesTableSeeder primero.");
        }

        // Crea un usuario para el rol 'secretaria'
        $secretaryUser = User::firstOrCreate(
            ['email' => 'secretaria@example.com'],
            [
                'name' => 'Secretaria Oficina Principal',
                'password' => Hash::make('password'),
                'office_id' => $office1->id,
                'email_verified_at' => now(),
            ]
        );
        $secretaryRole = Role::where('name', 'secretaria')->first();
        if ($secretaryRole) {
            $secretaryUser->assignRole($secretaryRole);
            $this->command->info("Usuario '{$secretaryUser->name}' creado y asignado al rol 'secretaria'.");
        } else {
            $this->command->warn("Rol 'secretaria' no encontrado. Asegúrate de ejecutar RolesTableSeeder primero.");
        }

        // Crea un usuario para el rol 'jefe_oficina'
        $headUser = User::firstOrCreate(
            ['email' => 'jefe.rrhh@example.com'],
            [
                'name' => 'Jefe de Recursos Humanos',
                'password' => Hash::make('password'),
                'office_id' => $office2->id,
                'email_verified_at' => now(),
            ]
        );
        $headRole = Role::where('name', 'jefe_oficina')->first();
        if ($headRole) {
            $headUser->assignRole($headRole);
            $this->command->info("Usuario '{$headUser->name}' creado y asignado al rol 'jefe_oficina'.");
        } else {
            $this->command->warn("Rol 'jefe_oficina' no encontrado. Asegúrate de ejecutar RolesTableSeeder primero.");
        }

        $this->command->info('Usuarios de prueba creados y roles asignados exitosamente.');
    }
}
