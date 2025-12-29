<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\DocumentType;
use App\Enums\DocumentPriority;

class StoreDocumentRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        // Aquí verificamos si el usuario está logueado.
        // Más adelante podemos agregar: return $this->user()->can('crear-documento');
        return auth()->check();
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'min:5', 'max:255'],
            
            'description' => ['nullable', 'string', 'max:1000'],
            
            // Validamos contra los Enums que acabamos de crear
            'type' => ['required', Rule::enum(DocumentType::class)],
            'priority' => ['required', Rule::enum(DocumentPriority::class)],
            
            // Archivos: PDF, Word o Imágenes. Máximo 10MB (10240 KB)
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
            
            // Si el usuario quiere derivar de inmediato (opcional)
            'to_office_id' => ['nullable', 'exists:offices,id'],
            
            // Si pertenece a un expediente existente (opcional)
            'expediente_id' => ['nullable', 'exists:expedientes,id'],
        ];
    }

    /**
     * Mensajes de error personalizados (opcional pero recomendado para usuarios).
     */
    public function messages(): array
    {
        return [
            'subject.required' => 'El asunto del documento es obligatorio.',
            'type.enum' => 'El tipo de documento seleccionado no es válido.',
            'file.required' => 'Debes adjuntar el documento digital.',
            'file.max' => 'El archivo no puede pesar más de 10MB.',
            'file.mimes' => 'Solo se permiten archivos PDF, Word o Imágenes.',
        ];
    }
}