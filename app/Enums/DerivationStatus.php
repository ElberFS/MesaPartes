<?php

namespace App\Enums;

enum DerivationStatus: string
{
    case ENVIADO = 'enviado';
    case RECIBIDO = 'recibido';
    case ATENDIDO = 'atendido';
    case CONOCIMIENTO = 'de_conocimiento';
    case RECHAZADO = 'rechazado';
    case ARCHIVADO = 'archivado';

    public function label(): string
    {
        return match($this) {
            self::ENVIADO => 'Enviado',
            self::RECIBIDO => 'Recibido',
            self::ATENDIDO => 'Atendido',
            self::CONOCIMIENTO => 'De Conocimiento',
            self::RECHAZADO => 'Rechazado',
            self::ARCHIVADO => 'Archivado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ENVIADO => 'blue',    // text-blue-600 bg-blue-100
            self::RECIBIDO => 'yellow', // text-yellow-600 bg-yellow-100
            self::ATENDIDO => 'green',  // text-green-600 bg-green-100
            self::CONOCIMIENTO => 'gray',
            self::RECHAZADO => 'red',
            self::ARCHIVADO => 'stone', // O dark
        };
    }
}