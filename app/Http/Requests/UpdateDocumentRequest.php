<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Importa la clase Rule para validaciones condicionales

class UpdateDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Determina si el usuario está autorizado para hacer esta solicitud.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Solo la 'secretaria' o el 'administrador' deberían poder actualizar documentos.
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
        // El ID del documento a ignorar en la validación 'unique' del código.
        // Asumimos que el ID del documento se pasa como un parámetro de ruta (ej. /documents/{document})
        // o que el componente Livewire tiene una propiedad $document (modelo) de donde se puede obtener el ID.
        $documentId = $this->route('document') ? $this->route('document')->id : null;

        return [
            'code' => [
                'required',
                'string',
                'max:255',
                // La regla unique ignora el ID del documento actual.
                Rule::unique('documents', 'code')->ignore($documentId),
            ],
            'subject' => ['required', 'string', 'max:255'],
            'origin_type' => ['required', 'in:internal,external'],
            'origin_office_id' => ['required_if:origin_type,internal', 'nullable', 'integer', 'exists:offices,id'],
            'external_person_id' => ['required_if:origin_type,external', 'nullable', 'integer', 'exists:external_people,id'],
            'reference' => ['nullable', 'string', 'max:255'],
            'origin_in_charge' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            // El archivo es opcional al actualizar, pero si se proporciona, se valida.
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'priority_id' => ['required', 'integer', 'exists:priorities,id'],
            'registration_date' => ['required', 'date'],
            'status' => ['required', 'in:in_process,responded,archived'], // El estado también puede ser actualizado
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
            'code.required' => 'El código del documento es obligatorio.',
            'code.unique' => 'Ya existe un documento con este código.',
            'subject.required' => 'El asunto del documento es obligatorio.',
            'origin_type.required' => 'El tipo de origen es obligatorio.',
            'origin_type.in' => 'El tipo de origen debe ser interno o externo.',
            'origin_office_id.required_if' => 'Debe seleccionar una oficina de origen para documentos internos.',
            'origin_office_id.exists' => 'La oficina de origen seleccionada no es válida.',
            'external_person_id.required_if' => 'Debe seleccionar una persona externa de origen para documentos externos.',
            'external_person_id.exists' => 'La persona externa seleccionada no es válida.',
            'file.file' => 'El adjunto debe ser un archivo.',
            'file.mimes' => 'El archivo debe ser de tipo PDF, DOC o DOCX.',
            'file.max' => 'El tamaño del archivo no debe exceder los 10MB.',
            'priority_id.required' => 'La prioridad del documento es obligatoria.',
            'priority_id.exists' => 'La prioridad seleccionada no es válida.',
            'registration_date.required' => 'La fecha de registro es obligatoria.',
            'registration_date.date' => 'La fecha de registro debe ser una fecha válida.',
            'status.required' => 'El estado del documento es obligatorio.',
            'status.in' => 'El estado del documento no es válido.',
        ];
    }
}
