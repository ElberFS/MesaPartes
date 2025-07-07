<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password; // Importa las reglas de contraseña de Laravel

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Determina si el usuario está autorizado para hacer esta solicitud.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Solo un administrador debería poder crear usuarios.
        // Asegúrate de que el usuario autenticado tiene el rol 'administrador'.
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            'selectedRole' => ['required', 'string', 'exists:roles,name'],
            'selectedOfficeId' => ['required', 'integer', 'exists:offices,id'],
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
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'selectedRole.required' => 'Debe seleccionar un rol para el usuario.',
            'selectedRole.exists' => 'El rol seleccionado no es válido.',
            'selectedOfficeId.required' => 'Debe seleccionar una oficina para el usuario.',
            'selectedOfficeId.integer' => 'El ID de la oficina debe ser un número entero.',
            'selectedOfficeId.exists' => 'La oficina seleccionada no es válida.',
        ];
    }
}
