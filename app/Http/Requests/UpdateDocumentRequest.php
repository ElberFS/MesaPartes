<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\DocumentType;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::enum(DocumentType::class)],
            // Nota: El archivo (file) suele ser nullable en update, 
            // solo se valida si el usuario sube uno nuevo para reemplazar.
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg', 'max:10240'],
        ];
    }
}