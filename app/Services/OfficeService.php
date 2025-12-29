<?php

namespace App\Services;

use App\Models\Office;

class OfficeService
{
    public function createOffice(array $data): Office
    {
        return Office::create([
            'name' => $data['name'],
            'acronym' => strtoupper($data['acronym']), // Aseguramos mayúsculas
            'is_active' => $data['is_active'] ?? true,
        ]);
    }
    
    public function toggleStatus(Office $office): bool
    {
        // Activar/Desactivar oficina
        $office->update(['is_active' => !$office->is_active]);
        return $office->is_active;
    }

    public function updateOffice(Office $office, array $data): Office
    {
        $office->update([
            'name' => $data['name'],
            'acronym' => strtoupper($data['acronym']),
            'is_active' => $data['is_active'] ?? $office->is_active,
        ]);
        
        return $office;
    }

    /**
     * Eliminar oficina (Solo si está vacía).
     */
    public function deleteOffice(Office $office): void
    {
        // 1. Validación de integridad
        if ($office->users()->exists()) {
            throw new \Exception("No puedes eliminar una oficina que tiene usuarios asignados. Primero reasígnalos.");
        }

        if ($office->documents()->exists()) { // Asumiendo relación documents() en el modelo Office
            throw new \Exception("No puedes eliminar una oficina que tiene documentos históricos. Mejor desactívala.");
        }

        $office->delete();
    }
}