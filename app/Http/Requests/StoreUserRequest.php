<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo un admin debería poder crear usuarios
        // return auth()->user()->can('crear-usuarios'); 
        return auth()->check(); 
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            
            // Validar que el Rol exista en Spatie
            'role' => ['required', 'exists:roles,name'],
            
            // Validar asignación a oficina
            'office_id' => ['required', 'exists:offices,id'],
            'is_boss' => ['boolean'], // ¿Es el jefe de esa oficina?
        ];
    }
}