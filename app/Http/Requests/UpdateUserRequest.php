<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Importa la clase Rule para validaciones condicionales
use Illuminate\Validation\Rules\Password; // Importa las reglas de contraseña de Laravel

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Determina si el usuario está autorizado para hacer esta solicitud.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Solo un administrador debería poder actualizar usuarios.
        return auth()->check() && auth()->user()->hasRole('administrador');
    }

    /**
     * Get the validation rules that apply to the request.
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        // El ID del usuario a ignorar en la validación 'unique' del email.
        // Asumimos que el ID del usuario se pasa como un parámetro de ruta (ej. /users/{user})
        // o que el componente Livewire tiene una propiedad $user (modelo) de donde se puede obtener el ID.
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                // La regla unique ignora el ID del usuario actual.
                Rule::unique('users', 'email')->ignore($userId),
            ],
            // Contraseña es opcional para la actualización. Si se proporciona, se valida.
            'password' => ['nullable', 'string', Password::defaults(), 'confirmed'],
            'selectedRole' => ['required', 'string', 'exists:roles,name'], // El rol debe existir
            'selectedOfficeId' => ['required', 'integer', 'exists:offices,id'], // La oficina debe existir
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * Obtiene los mensajes de error para las reglas de validación definidas.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede exceder los :max caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'selectedRole.required' => 'Debe seleccionar un rol para el usuario.',
            'selectedRole.exists' => 'El rol seleccionado no es válido.',
            'selectedOfficeId.required' => 'Debe seleccionar una oficina para el usuario.',
            'selectedOfficeId.integer' => 'El ID de la oficina debe ser un número entero.',
            'selectedOfficeId.exists' => 'La oficina seleccionada no es válida.',
        ];
    }
}
