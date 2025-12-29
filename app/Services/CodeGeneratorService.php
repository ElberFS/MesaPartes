<?php

namespace App\Services;

use App\Models\Office;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CodeGeneratorService
{
    /**
     * Genera un código tipo: PREFIJO-SEQ-AÑO-SIGLAS
     * Ejemplo: D-0001-2025-SG
     */
    public function generate(string $prefix, string $tableName, Office $office): string
    {
        $year = Carbon::now()->year;
        
        // Buscamos el último registro creado por ESTA oficina en ESTE año
        // Asumimos que la tabla tiene columnas 'code', 'office_id' y 'created_at'
        $lastRecord = DB::table($tableName)
            ->where('office_id', $office->id)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        // Si existe, extraemos el correlativo (el segundo segmento), si no, empezamos en 1
        $sequence = 1;
        
        if ($lastRecord) {
            // Ejemplo código: D-0045-2025-SG. Explode por '-' da array. El índice 1 es "0045"
            $parts = explode('-', $lastRecord->code);
            if (isset($parts[1]) && is_numeric($parts[1])) {
                $sequence = intval($parts[1]) + 1;
            }
        }

        // Formateamos: D-0001-2025-SG
        return sprintf(
            "%s-%04d-%s-%s",
            $prefix,
            $sequence,
            $year,
            strtoupper($office->acronym)
        );
    }
}