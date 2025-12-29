<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentReference extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_type',
        'source_id',
        'target_type',
        'target_id',
    ];

    /**
     * Modelo origen (Document o Expediente)
     */
    public function source()
    {
        return $this->morphTo();
    }

    /**
     * Modelo destino (Document o Expediente)
     */
    public function target()
    {
        return $this->morphTo();
    }
}
