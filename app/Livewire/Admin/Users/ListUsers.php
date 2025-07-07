<?php

namespace App\Livewire\Admin\Users;

use App\Models\User; // Importa el modelo User
use Livewire\Component;
use Livewire\WithPagination; // Trait para paginación

class ListUsers extends Component
{
    use WithPagination; // Habilita la paginación en el componente

    public $search = ''; // Propiedad para el término de búsqueda
    public $perPage = 10; // Propiedad para el número de elementos por página

    public $confirmingUserDeletion = false; // Estado para el modal de confirmación de eliminación
    public $userToDeleteId = null; // ID del usuario a eliminar

    /**
     * Renderiza la vista Blade asociada a este componente.
     * Obtiene los usuarios aplicando búsqueda y paginación.
     */
    public function render()
    {
        $users = User::query()
            ->with(['roles', 'office']) // Carga las relaciones de roles y oficina para evitar N+1
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->paginate($this->perPage);

        return view('livewire.admin.users.list-users', [
            'users' => $users,
        ]);
    }

    /**
     * Reinicia la paginación cuando cambia el término de búsqueda.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Muestra el modal de confirmación para eliminar un usuario.
     *
     * @param int $userId El ID del usuario a eliminar.
     */
    public function confirmUserDeletion($userId)
    {
        $this->confirmingUserDeletion = true;
        $this->userToDeleteId = $userId;
    }

    /**
     * Elimina el usuario confirmado.
     */
    public function deleteUser()
    {
        if ($this->userToDeleteId) {
            $user = User::find($this->userToDeleteId);
            if ($user) {
                try {
                    $user->delete();
                    session()->flash('status', 'Usuario eliminado exitosamente.');
                } catch (\Illuminate\Database\QueryException $e) {
                    // Manejo de errores si el usuario tiene relaciones que impiden la eliminación
                    session()->flash('error', 'No se puede eliminar el usuario debido a restricciones de integridad referencial.');
                }
            }
        }

        $this->confirmingUserDeletion = false; // Cierra el modal
        $this->userToDeleteId = null; // Resetea el ID
    }
}
