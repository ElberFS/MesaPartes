<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $allPermissions = Permission::all();

        // SUPERADMIN
        $superAdmin = Role::firstOrCreate([
            'name' => 'superadmin',
            'guard_name' => 'web',
        ]);
        $superAdmin->syncPermissions($allPermissions);

        // ADMIN
        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $admin->syncPermissions([
            'crear-documento',
            'editar-documento',
            'ver-documento',
            'eliminar-documento',
            'derivar-documento',
            'archivar-documento',
            'ver-usuarios',
            'crear-usuarios',
            'editar-usuarios',
            'ver-reportes',
            'exportar-reportes',
            'configurar-sistema',
        ]);

        // JEFE
        $jefe = Role::firstOrCreate([
            'name' => 'jefe',
            'guard_name' => 'web',
        ]);
        $jefe->syncPermissions([
            'crear-documento',
            'editar-documento',
            'ver-documento',
            'derivar-documento',
            'archivar-documento',
            'ver-reportes',
            'ver-usuarios',
        ]);

        // SECRETARIA
        $secretaria = Role::firstOrCreate([
            'name' => 'secretaria',
            'guard_name' => 'web',
        ]);
        $secretaria->syncPermissions([
            'crear-documento',
            'ver-documento',
            'derivar-documento',
        ]);
    }
}
