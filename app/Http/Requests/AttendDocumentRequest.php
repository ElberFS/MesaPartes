<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // Es obligatorio explicar cómo se resolvió
            'conclusion' => ['required', 'string', 'min:10', 'max:1000'],
            
            // Opcional: Adjuntar un archivo de respuesta (ej: Oficio de respuesta)
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg', 'max:10240'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'conclusion.required' => 'Debes escribir una conclusión o detalle de la atención.',
        ];
    }
}