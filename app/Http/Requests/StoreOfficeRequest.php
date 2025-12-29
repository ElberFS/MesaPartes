<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfficeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:offices,name'],
            'acronym' => ['required', 'string', 'max:10', 'uppercase', 'unique:offices,acronym'],
            'is_active' => ['boolean'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'acronym.unique' => 'Ya existe una oficina con estas siglas.',
        ];
    }
}