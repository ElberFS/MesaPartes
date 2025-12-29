<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Office;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DocumentService
{
    public function __construct(
        protected CodeGeneratorService $codeGenerator
    ) {}

    /**
     * Crea un documento físico y digital.
     */
    public function createDocument(array $data, UploadedFile $file, Office $office): Document
    {
        return DB::transaction(function () use ($data, $file, $office) {
            
            // 1. Generar Código
            $code = $this->codeGenerator->generate('D', 'documents', $office);

            // 2. Subir Archivo (Storage/app/public/documents/2025/CODE.pdf)
            $year = Carbon::now()->year;
            $extension = $file->getClientOriginalExtension();
            $fileName = "{$code}.{$extension}";
            
            // storeAs devuelve el path relativo
            $filePath = $file->storeAs("documents/{$year}", $fileName, 'public');

            // 3. Crear Registro en BD
            return Document::create([
                'code' => $code,
                'subject' => $data['subject'],
                'description' => $data['description'] ?? null,
                'type' => $data['type'],         // Viene del Enum
                'priority' => $data['priority'], // Viene del Enum
                'file_path' => $filePath,
                'office_id' => $office->id,      // Oficina Origen
                'expediente_id' => $data['expediente_id'] ?? null,
            ]);
        });
    }

    /**
     * Actualizar metadatos (solo admin o creador antes de envío).
     */
    public function updateDocument(Document $document, array $data): Document
    {
        $document->update([
            'subject' => $data['subject'],
            'description' => $data['description'] ?? $document->description,
            'type' => $data['type'],
        ]);
        
        return $document;
    }
}