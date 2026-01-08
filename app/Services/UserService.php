<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Models\Office;


class UserService
{
    public function __construct(
        protected User $model
    ) {}

    /**
     * Listar usuarios con búsqueda
     */
    public function getAll(int $perPage = 10, string $search = ''): LengthAwarePaginator
    {
        return $this->model
            ->with('offices')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage);
    }


    /**
     * Crear usuario
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {

            $data['password'] = Hash::make($data['password']);

            return $this->model->create($data);
        });
    }

    /**
     * Actualizar usuario
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {

            // Si no se envía password, no se actualiza
            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);

            return $user->refresh();
        });
    }

    /**
     * Eliminar usuario
     */
    public function delete(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            return $user->delete();
        });
    }

    
    public function syncOffices(User $user, array $offices): void
    {
        if (empty($offices)) {
            $user->offices()->detach();
            return;
        }

        $syncData = [];

        foreach ($offices as $officeId => $data) {
            $syncData[$officeId] = [
                'is_boss' => $data['is_boss'] ?? false,
                'assigned_at' => Carbon::now(),
            ];
        }

        $user->offices()->sync($syncData);
    }


}
