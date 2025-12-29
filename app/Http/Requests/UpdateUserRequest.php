<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        // $this->user es el objeto (o ID) que viene en la ruta: /users/{user}
        $userId = $this->route('user') instanceof User ? $this->route('user')->id : $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            // Email único, ignorando el ID actual
            'email' => ['required', 'email', Rule::unique('users')->ignore($userId)],
            // La contraseña es NULLABLE (si no escribe nada, no se cambia)
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            
            'role' => ['required', 'exists:roles,name'],
            'office_id' => ['required', 'exists:offices,id'],
            'is_boss' => ['boolean'],
        ];
    }
}