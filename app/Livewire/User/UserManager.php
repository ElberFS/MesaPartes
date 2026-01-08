<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Office;
use App\Services\UserService;
use App\Http\Requests\UserRequest;

class UserManager extends Component
{
    use WithPagination;

    public $search = '';
    public $userId;

    public $name;
    public $email;
    public $password;
    public $password_confirmation;

    public $role;
    public $office_id;
    public $is_boss = false;

    public bool $isEditing = false;
    public bool $showModal = false;

    protected $paginationTheme = 'tailwind';

    public function render(UserService $service)
    {
        return view('livewire.user.user-manager', [
            'users' => $service->getAll(10, $this->search),
            'offices' => Office::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        $this->reset([
            'name',
            'email',
            'password',
            'password_confirmation',
            'role',
            'office_id',
            'is_boss',
            'userId',
            'isEditing',
        ]);

        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $user = User::with('offices')->findOrFail($id);

        $this->userId = $user->id;
        $this->isEditing = true;

        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->getRoleNames()->first();

        $office = $user->offices->first();
        $this->office_id = $office?->id;
        $this->is_boss = $office?->pivot->is_boss ?? false;

        $this->password = null;
        $this->password_confirmation = null;

        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(UserService $service)
    {
        $request = new UserRequest();

        if ($this->userId) {
            $request->user = $this->userId;
        }

        $validated = $this->validate($request->rules());

        /** Crear o actualizar */
        if ($this->isEditing) {
            $user = User::findOrFail($this->userId);
            $service->update($user, $validated);
        } else {
            $user = $service->create($validated);
        }

        /** Rol (sin superadmin) */
        if ($this->role) {
            $user->syncRoles([$this->role]);
        }

        /**
         * Oficina
         * - Si viene office_id → sincroniza
         * - Si viene vacío → desasigna todas
         */
        if ($this->office_id) {
            $service->syncOffices($user, [
                $this->office_id => [
                    'is_boss' => $this->is_boss,
                ],
            ]);
        } else {
            $user->offices()->detach();
        }

        $this->reset([
            'name',
            'email',
            'password',
            'password_confirmation',
            'role',
            'office_id',
            'is_boss',
            'userId',
            'isEditing',
        ]);

        $this->resetValidation();
        $this->showModal = false;

        session()->flash('status', 'Operación realizada con éxito.');
    }

    public function delete($id, UserService $service)
    {
        $user = User::findOrFail($id);
        $service->delete($user);
    }
}
