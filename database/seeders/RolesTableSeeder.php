<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Ejecuta los seeders de la base de datos para crear los roles del sistema.
     */
    public function run(): void
    {
        // Desactiva la caché de permisos para que los nuevos roles estén disponibles inmediatamente.
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Roles a crear para el Sistema de Gestión Documental.
        // Los usuarios no crearán cuentas, solo el administrador, por lo que no se necesita un rol 'otro'.
        $roles = [
            'secretaria',
            'administrador',
            'jefe_oficina',
        ];

        // Este bucle foreach es el que CREA los roles en la base de datos.
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
            $this->command->info("Rol '{$roleName}' asegurado/creado.");
        }

        $this->command->info('Roles del Sistema de Gestión Documental creados exitosamente o ya existentes.');
    }
}
