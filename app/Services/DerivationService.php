<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentDerivation;
use App\Models\Office;
use App\Enums\DerivationStatus;
use Illuminate\Support\Facades\DB;

class DerivationService
{
    /**
     * Derivar un documento de Oficina A -> Oficina B
     */
    public function derive(Document $document, Office $fromOffice, Office $toOffice, ?string $comment = null, bool $isCopy = false): DocumentDerivation
    {
        return DB::transaction(function () use ($document, $fromOffice, $toOffice, $comment, $isCopy) {
            
            // Si NO es copia (es el original), debemos verificar si ya estaba en algún lado
            // y marcar el anterior como 'derivado' o procesado si fuera necesario.
            // Para simplicidad inicial, creamos el registro de movimiento.

            return DocumentDerivation::create([
                'document_id' => $document->id,
                'from_office_id' => $fromOffice->id,
                'to_office_id' => $toOffice->id,
                'sent_at' => now(),
                'status' => $isCopy ? DerivationStatus::CONOCIMIENTO : DerivationStatus::ENVIADO,
                'comment' => $comment,
            ]);
        });
    }

    /**
     * Marcar como RECIBIDO (cuando el usuario de destino abre la bandeja).
     */
    public function receive(DocumentDerivation $derivation): void
    {
        if ($derivation->status === DerivationStatus::ENVIADO) {
            $derivation->update([
                'status' => DerivationStatus::RECIBIDO,
                'received_at' => now(), // Asegúrate de tener esta columna en la migración o usa updated_at
            ]);
        }
    }

    /**
     * ATENDER (Cerrar) un trámite.
     */
    public function attend(DocumentDerivation $derivation, string $conclusion, ?string $attachmentPath = null): void
    {
        $derivation->update([
            'status' => DerivationStatus::ATENDIDO,
            'conclusion' => $conclusion, // Columna extra que sugerimos en requests
            'attachment_path' => $attachmentPath,
            'attended_at' => now(),
        ]);
    }
}