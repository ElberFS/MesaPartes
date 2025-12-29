<?php

namespace App\Services;

use App\Models\Expediente;
use App\Models\Office;
use Illuminate\Support\Facades\DB;

class ExpedienteService
{
    public function __construct(
        protected CodeGeneratorService $codeGenerator
    ) {}

    public function createExpediente(array $data, Office $office): Expediente
    {
        return DB::transaction(function () use ($data, $office) {
            
            // Genera E-0001-2025-SG
            $code = $this->codeGenerator->generate('E', 'expedientes', $office);

            return Expediente::create([
                'code' => $code,
                'subject' => $data['subject'],
                'description' => $data['description'] ?? null,
                'office_id' => $office->id,
                'tags' => $data['tags'] ?? null,
                'status' => 'abierto', // Podrías usar un Enum aquí también
            ]);
        });
    }
}