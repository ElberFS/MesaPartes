<?php

namespace App\Services;

use App\Models\User;
use App\Models\Office;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Crea usuario, asigna rol y lo vincula a una oficina.
     */
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            
            // 1. Crear el registro base del usuario
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // 2. Asignar Rol (Spatie)
            // Asumimos que $data['role'] viene del select (ej: 'jefe_oficina')
            $user->assignRole($data['role']);

            // 3. Vincular a la Oficina (Tabla Pivote)
            // Si es jefe, marcamos is_boss = true
            $user->offices()->attach($data['office_id'], [
                'is_boss' => $data['is_boss'] ?? false,
                'assigned_at' => now(),
            ]);

            return $user;
        });
    }

    /**
     * Actualizar usuario, roles y oficina.
     */
    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            // Solo actualizamos password si el usuario escribió una nueva
            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            // Actualizar Rol
            $user->syncRoles([$data['role']]);

            // Actualizar Oficina y Jefatura
            // syncWithPivotValues borra la relación anterior y pone la nueva
            $user->offices()->syncWithPivotValues([$data['office_id']], [
                'is_boss' => $data['is_boss'] ?? false,
                'assigned_at' => now(), // O mantener la fecha original si prefieres
            ]);

            return $user;
        });
    }

    /**
     * Eliminar usuario (con validaciones de seguridad).
     */
    public function deleteUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            // 1. Evitar auto-eliminación
            if ($user->id === auth()->id()) {
                throw new \Exception("No puedes eliminar tu propia cuenta.");
            }

            // 2. Verificar si tiene documentos firmados o creados (Opcional)
            // if ($user->documents()->exists()) { throw ... }

            // 3. Eliminar (Esto eliminará relaciones en pivotes automáticamente por FK cascades si están configuradas, 
            // pero Spatie requiere detach manual a veces o se limpia solo).
            $user->roles()->detach();
            $user->offices()->detach();
            
            $user->delete();
        });
    }
}