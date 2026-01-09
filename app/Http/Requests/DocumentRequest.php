<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'expediente_id' => [
                'nullable',
                'exists:expedientes,id',
            ],

            'file' => [
                $this->document ? 'nullable' : 'required',
                'file',
                'mimes:pdf',
                'max:10240', // 10MB
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'subject' => trim($this->subject),
        ]);
    }
}
