<?php

namespace App\Enums;

enum DocumentPriority: string
{
    case BAJA = 'baja';
    case NORMAL = 'normal';
    case ALTA = 'alta';
    case URGENTE = 'urgente';

    public function label(): string
    {
        return match($this) {
            self::BAJA => 'Baja',
            self::NORMAL => 'Normal',
            self::ALTA => 'Alta',
            self::URGENTE => 'Urgente 🔥',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::BAJA => 'gray',
            self::NORMAL => 'blue',
            self::ALTA => 'orange',
            self::URGENTE => 'red',
        };
    }
}