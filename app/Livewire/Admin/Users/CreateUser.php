<?php

namespace App\Livewire\Admin\Users;

use App\Models\User; // Importa el modelo User
use App\Models\Office; // Importa el modelo Office
use Livewire\Component;
use App\Http\Requests\StoreUserRequest; // Importa el Form Request para la validación
use Illuminate\Support\Facades\Hash; // Para hashear contraseñas
use Spatie\Permission\Models\Role; // Importa el modelo Role de Spatie
use Illuminate\Validation\ValidationException; // Importar para manejar excepciones de validación

class CreateUser extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $selectedRole = ''; // Para el rol seleccionado
    public $selectedOfficeId = ''; // Para el ID de la oficina seleccionada

    public $roles = []; // Almacenará todos los roles disponibles
    public $offices = []; // Almacenará todas las oficinas disponibles

    /**
     * Método de montaje del componente. Se ejecuta una vez al inicio.
     * Carga todos los roles y oficinas disponibles para los selectores.
     */
    public function mount()
    {
        // Obtiene solo los nombres de los roles.
        // Si necesitas el ID del rol, quizás debas almacenar objetos Role o un array asociativo [id => name].
        // Para este caso, 'name' es suficiente ya que Spatie asigna por nombre.
        $this->roles = Role::pluck('name')->toArray();
        $this->offices = Office::all(); // Obtiene todas las oficinas
    }

    /**
     * Guarda el nuevo usuario en la base de datos.
     */
    public function save()
    {
        // 1. Instanciar el Form Request para obtener sus reglas de validación y mensajes personalizados.
        $storeUserRequest = new StoreUserRequest();

        try {
            // 2. Realizar la validación de Livewire, pasando las reglas y mensajes del Form Request.
            // Livewire automáticamente validará las propiedades públicas del componente.
            $validatedData = $this->validate(
                $storeUserRequest->rules(),
                $storeUserRequest->messages()
            );

            // 3. Crea el usuario con los datos validados.
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']), // Hashea la contraseña
                'office_id' => $validatedData['selectedOfficeId'], // Asigna la oficina
            ]);

            // 4. Asigna el rol al usuario.
            $user->assignRole($validatedData['selectedRole']);

            // 5. Resetea las propiedades del formulario después de un registro exitoso.
            $this->reset(['name', 'email', 'password', 'password_confirmation', 'selectedRole', 'selectedOfficeId']);

            // 6. Emite un mensaje de sesión flash.
            session()->flash('status', 'Usuario creado exitosamente y rol asignado.');

            // 7. Redirige a la lista de usuarios.
            return $this->redirect(route('users.index'), navigate: true);

        } catch (ValidationException $e) {
            // Livewire maneja la visualización de errores automáticamente en la vista.
            // Aquí puedes logear la excepción si es necesario para depuración.
            // Log::error('Error de validación al crear usuario: ' . $e->getMessage(), $e->errors());
            throw $e; // Re-lanza la excepción para que Livewire la capture y muestre los errores.
        } catch (\Exception $e) {
            // Captura cualquier otra excepción inesperada.
            session()->flash('error', 'Ocurrió un error inesperado al crear el usuario: ' . $e->getMessage());
            // Log::error('Error inesperado al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * Renderiza la vista Blade asociada a este componente.
     */
    public function render()
    {
        return view('livewire.admin.users.create-user');
    }
}
