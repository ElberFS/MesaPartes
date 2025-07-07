<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Importa la clase Rule para validaciones condicionales

class UpdateOfficeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Determina si el usuario está autorizado para hacer esta solicitud.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Por ahora, permitimos que cualquier usuario autenticado realice esta solicitud.
        // Aquí puedes añadir lógica de autorización más compleja (ej. si el usuario tiene el rol 'administrador').
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        // La validación 'unique' debe ignorar el ID de la oficina que se está actualizando.
        // Asumimos que el ID de la oficina se pasa como un parámetro de ruta (ej. /offices/{office})
        // o como una propiedad en el componente Livewire que se pasa al request.
        // Para este ejemplo, asumimos que el ID se puede obtener de la ruta.
        $officeId = $this->route('office') ? $this->route('office')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // La regla unique ignora el ID de la oficina actual.
                Rule::unique('offices', 'name')->ignore($officeId),
            ],
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
            'name.required' => 'El nombre de la oficina es obligatorio.',
            'name.string' => 'El nombre de la oficina debe ser una cadena de texto.',
            'name.max' => 'El nombre de la oficina no puede exceder los :max caracteres.',
            'name.unique' => 'Ya existe una oficina con este nombre.',
        ];
    }
}
