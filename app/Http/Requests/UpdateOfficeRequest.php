<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Office;

class UpdateOfficeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $officeId = $this->route('office') instanceof Office ? $this->route('office')->id : $this->route('office');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('offices')->ignore($officeId)],
            'acronym' => ['required', 'string', 'max:10', 'uppercase', Rule::unique('offices')->ignore($officeId)],
            'is_active' => ['boolean'],
        ];
    }
}