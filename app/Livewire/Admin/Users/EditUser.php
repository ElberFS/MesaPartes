<?php

namespace App\Livewire\Admin\Users;

use App\Models\User; // Importa el modelo User
use App\Models\Office; // Importa el modelo Office
use Livewire\Component;
use App\Http\Requests\UpdateUserRequest; // Importa el Form Request para la actualización
use Illuminate\Support\Facades\Hash; // Para hashear contraseñas
use Spatie\Permission\Models\Role; // Importa el modelo Role de Spatie
use Illuminate\Validation\ValidationException; // Importar para manejar excepciones de validación

class EditUser extends Component
{
    public User $user; // Propiedad para inyectar el modelo User
    public $name = '';
    public $email = '';
    public $password = ''; // Opcional, solo si se quiere cambiar
    public $password_confirmation = '';
    public $selectedRole = '';
    public $selectedOfficeId = '';

    public $roles = []; // Almacenará todos los roles disponibles
    public $offices = []; // Almacenará todas las oficinas disponibles

    /**
     * Método de montaje del componente. Se ejecuta una vez al inicio.
     * Carga los datos del usuario, roles y oficinas.
     *
     * @param User $user El modelo de usuario inyectado por Livewire.
     */
    public function mount(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedOfficeId = $user->office_id; // Carga la oficina actual del usuario

        // Carga el rol actual del usuario
        $currentRole = $user->roles->first();
        $this->selectedRole = $currentRole ? $currentRole->name : '';

        // Carga todos los roles y oficinas disponibles para los selectores
        $this->roles = Role::pluck('name')->toArray();
        $this->offices = Office::all();
    }

    /**
     * Actualiza el usuario en la base de datos.
     */
    public function update()
    {
        // 1. Instanciar el Form Request para obtener sus reglas de validación y mensajes personalizados.
        $updateUserRequest = new UpdateUserRequest();

        try {
            // 2. Realizar la validación de Livewire, pasando las reglas y mensajes del Form Request.
            // Livewire automáticamente validará las propiedades públicas del componente.
            // Es crucial pasar el ID del usuario al método rules() para que la regla 'unique' lo ignore.
            $validatedData = $this->validate(
                $updateUserRequest->rules($this->user->id), // Pasa el ID del usuario actual
                $updateUserRequest->messages()
            );

            // 3. Prepara los datos para la actualización
            $updateData = [
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'office_id' => $validatedData['selectedOfficeId'],
            ];

            // 4. Si se proporcionó una nueva contraseña, hashearla y añadirla a los datos de actualización
            if (!empty($validatedData['password'])) {
                $updateData['password'] = Hash::make($validatedData['password']);
            }

            // 5. Actualiza los datos del usuario
            $this->user->update($updateData);

            // 6. Sincroniza los roles del usuario (elimina los antiguos y asigna el nuevo)
            $this->user->syncRoles($validatedData['selectedRole']);

            // 7. Resetea los campos de contraseña después de la actualización (por seguridad)
            $this->reset(['password', 'password_confirmation']);

            // 8. Emite un mensaje de sesión flash
            session()->flash('status', 'Usuario actualizado exitosamente.');

            // 9. Redirige al usuario a la lista de usuarios después de la actualización.
            return $this->redirect(route('users.index'), navigate: true);

        } catch (ValidationException $e) {
            // Livewire maneja la visualización de errores automáticamente en la vista.
            // Puedes logear la excepción si es necesario para depuración.
            // Log::error('Error de validación al actualizar usuario: ' . $e->getMessage(), $e->errors());
            throw $e; // Re-lanza la excepción para que Livewire la capture y muestre los errores.
        } catch (\Exception $e) {
            // Captura cualquier otra excepción inesperada.
            session()->flash('error', 'Ocurrió un error inesperado al actualizar el usuario: ' . $e->getMessage());
            // Log::error('Error inesperado al actualizar usuario: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * Renderiza la vista Blade asociada a este componente.
     */
    public function render()
    {
        return view('livewire.admin.users.edit-user');
    }
}
