<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Determina si el usuario está autorizado para hacer esta solicitud.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Solo la 'secretaria' o el 'administrador' deberían poder registrar documentos.
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
        return [
            'code' => ['required', 'string', 'max:255', 'unique:documents,code'], // Código único
            'subject' => ['required', 'string', 'max:255'],
            'origin_type' => ['required', 'in:internal,external'], // Debe ser 'internal' o 'external'
            'origin_office_id' => ['required_if:origin_type,internal', 'nullable', 'integer', 'exists:offices,id'], // Requerido si es interno
            'external_person_id' => ['required_if:origin_type,external', 'nullable', 'integer', 'exists:external_people,id'], // Requerido si es externo
            'reference' => ['nullable', 'string', 'max:255'],
            'origin_in_charge' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'], // Archivo PDF o Word, max 10MB
            'priority_id' => ['required', 'integer', 'exists:priorities,id'],
            'registration_date' => ['required', 'date'],
            // 'status' no se valida aquí, ya que se establece por defecto al crear.
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
            'file.required' => 'Debe adjuntar un archivo al documento.',
            'file.file' => 'El adjunto debe ser un archivo.',
            'file.mimes' => 'El archivo debe ser de tipo PDF, DOC o DOCX.',
            'file.max' => 'El tamaño del archivo no debe exceder los 10MB.',
            'priority_id.required' => 'La prioridad del documento es obligatoria.',
            'priority_id.exists' => 'La prioridad seleccionada no es válida.',
            'registration_date.required' => 'La fecha de registro es obligatoria.',
            'registration_date.date' => 'La fecha de registro debe ser una fecha válida.',
        ];
    }
}
