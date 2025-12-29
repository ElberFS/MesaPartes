<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeriveDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // En el futuro aquí validaremos: $user->can('derivar-documento')
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // La oficina destino es OBLIGATORIA y debe existir
            'to_office_id' => ['required', 'exists:offices,id', 'different:from_office_id'],
            
            // Comentario o indicación (Ej: "Para su atención urgente")
            'comment' => ['nullable', 'string', 'max:500'],
            
            // Prioridad específica para esta derivación (opcional, si cambia la original)
            'priority' => ['nullable', 'string', 'in:baja,normal,alta,urgente'],
            
            // ¿Es copia carbon? (CC) - Opcional para enviar copias informativas
            'cc_offices' => ['nullable', 'array'],
            'cc_offices.*' => ['exists:offices,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'to_office_id.required' => 'Debes seleccionar una oficina de destino.',
            'to_office_id.different' => 'No puedes derivar el documento a la misma oficina donde ya está.',
        ];
    }
}