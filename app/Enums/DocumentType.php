<?php

namespace App\Enums;

enum DocumentType: string
{
    case OFICIO = 'oficio';
    case MEMORANDUM = 'memorandum';
    case SOLICITUD = 'solicitud';
    case INFORME = 'informe';
    case CARTA = 'carta';
    case RESOLUCION = 'resolucion';
    case OTRO = 'otro';

    public function label(): string
    {
        return match($this) {
            self::OFICIO => 'Oficio',
            self::MEMORANDUM => 'Memorándum',
            self::SOLICITUD => 'Solicitud',
            self::INFORME => 'Informe Técnico',
            self::CARTA => 'Carta',
            self::RESOLUCION => 'Resolución',
            self::OTRO => 'Otro',
        };
    }
}