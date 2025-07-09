<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResponseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Determina si el usuario está autorizado para hacer esta solicitud.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Solo la 'secretaria' o el 'administrador' deberían poder actualizar respuestas.
        return auth()->check() && (auth()->user()->hasRole('secretaria') || auth()->user()->hasRole('administrador'));
    }

    /**
     * Get the validation rules that apply to the request.
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        // Obtener el ID de la respuesta desde la ruta (asumiendo inyección de modelo)
        $responseId = $this->route('response') ? $this->route('response')->id : null;

        return [
            // El asunto debe ser único excepto para la respuesta que se está editando
            'subject' => [
                'required',
                'string',
                'max:255',
                Rule::unique('responses')->ignore($responseId),
            ],
            'summary' => ['nullable', 'string'],
            'newFile' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'], // Archivo PDF o Word, max 10MB
            'is_final_response' => ['boolean'],
            'date' => ['required', 'date'],

            // Nuevas reglas para el destino
            'destination_type' => ['required', Rule::in(['internal', 'external'])],
            'destination_office_id' => [
                Rule::requiredIf($this->destination_type === 'internal'),
                'nullable', // Puede ser nulo si el tipo es externo
                'integer',
                'exists:offices,id',
            ],
            'destination_organization_name' => [
                Rule::requiredIf($this->destination_type === 'external'),
                'nullable',
                'string',
                'max:255',
            ],
            'destination_contact_person' => [
                Rule::requiredIf($this->destination_type === 'external'),
                'nullable',
                'string',
                'max:255',
            ],
            'destination_contact_role' => ['nullable', 'string', 'max:255'],
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
            'subject.required' => 'El asunto de la respuesta es obligatorio.',
            'subject.string' => 'El asunto de la respuesta debe ser una cadena de texto.',
            'subject.max' => 'El asunto de la respuesta no debe exceder los :max caracteres.',
            'subject.unique' => 'Ya existe una respuesta con este asunto.',
            'summary.string' => 'El resumen debe ser una cadena de texto.',
            'newFile.file' => 'El adjunto debe ser un archivo.',
            'newFile.mimes' => 'El archivo debe ser de tipo PDF, DOC o DOCX.',
            'newFile.max' => 'El tamaño del archivo no debe exceder los 10MB.',
            'is_final_response.boolean' => 'El campo "es respuesta final" debe ser verdadero o falso.',
            'date.required' => 'La fecha de la respuesta es obligatoria.',
            'date.date' => 'La fecha de la respuesta debe ser una fecha válida.',

            // Mensajes para los nuevos campos de destino
            'destination_type.required' => 'Debe seleccionar un tipo de destino (interno o externo).',
            'destination_type.in' => 'El tipo de destino seleccionado no es válido.',
            'destination_office_id.required_if' => 'Debe seleccionar una oficina de destino para respuestas internas.',
            'destination_office_id.integer' => 'El ID de la oficina de destino debe ser un número entero.',
            'destination_office_id.exists' => 'La oficina de destino seleccionada no es válida.',
            'destination_organization_name.required_if' => 'El nombre de la organización de destino es obligatorio para respuestas externas.',
            'destination_organization_name.string' => 'El nombre de la organización debe ser una cadena de texto.',
            'destination_organization_name.max' => 'El nombre de la organización no debe exceder los :max caracteres.',
            'destination_contact_person.required_if' => 'El nombre de la persona de contacto de destino es obligatorio para respuestas externas.',
            'destination_contact_person.string' => 'El nombre de la persona de contacto debe ser una cadena de texto.',
            'destination_contact_person.max' => 'El nombre de la persona de contacto no debe exceder los :max caracteres.',
            'destination_contact_role.string' => 'El cargo de la persona de contacto debe ser una cadena de texto.',
            'destination_contact_role.max' => 'El cargo de la persona de contacto no debe exceder los :max caracteres.',
        ];
    }
}
