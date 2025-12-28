<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Gestión de Documentos
            'crear-documento',
            'editar-documento',
            'ver-documento',
            'eliminar-documento',
            'derivar-documento',
            'archivar-documento',

            // Gestión de Usuarios
            'ver-usuarios',
            'crear-usuarios',
            'editar-usuarios',
            'eliminar-usuarios',

            // Reportes
            'ver-reportes',
            'exportar-reportes',

            // Configuración
            'configurar-sistema',
            'gestionar-roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}
