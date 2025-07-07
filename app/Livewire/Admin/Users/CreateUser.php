<?php

namespace App\Livewire\Admin\Users;

use App\Models\User; // Importa el modelo User
use App\Models\Office; // Importa el modelo Office
use Livewire\Component;
use App\Http\Requests\StoreUserRequest; // Importa el Form Request para la validación
use Illuminate\Support\Facades\Hash; // Para hashear contraseñas
use Spatie\Permission\Models\Role; // Importa el modelo Role de Spatie

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
        $this->roles = Role::pluck('name')->toArray(); // Obtiene solo los nombres de los roles
        $this->offices = Office::all(); // Obtiene todas las oficinas
    }

    /**
     * Guarda el nuevo usuario en la base de datos.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request  El Form Request para la validación.
     */
    public function save(StoreUserRequest $request)
    {
        $validatedData = $request->validated();

        // Crea el usuario
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']), // Hashea la contraseña
            'office_id' => $validatedData['selectedOfficeId'], // Asigna la oficina
        ]);

        // Asigna el rol al usuario
        $user->assignRole($validatedData['selectedRole']);

        // Resetea las propiedades del formulario
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'selectedRole', 'selectedOfficeId']);

        // Emite un mensaje de sesión flash
        session()->flash('status', 'Usuario creado exitosamente y rol asignado.');

        // Opcional: Redirigir a la lista de usuarios
        return $this->redirect(route('users.index'), navigate: true);
    }

    /**
     * Renderiza la vista Blade asociada a este componente.
     */
    public function render()
    {
        return view('livewire.admin.users.create-user');
    }
}
