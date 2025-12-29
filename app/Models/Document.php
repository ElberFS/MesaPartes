<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'subject',
        'description',
        'file_path',
        'office_id',
        'expediente_id',
    ];

    /**
     * Oficina origen del documento
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Expediente al que pertenece (opcional)
     */
    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

    /**
     * Historial de derivaciones
     */
    public function derivations()
    {
        return $this->hasMany(DocumentDerivation::class);
    }

    /**
     * Última derivación (estado actual)
     */
    public function lastDerivation()
    {
        return $this->hasOne(DocumentDerivation::class)->latestOfMany();
    }

    /**
     * Referencias donde este documento es origen
     */
    public function referencesFrom()
    {
        return $this->morphMany(DocumentReference::class, 'source');
    }

    /**
     * Referencias donde este documento es destino
     */
    public function referencesTo()
    {
        return $this->morphMany(DocumentReference::class, 'target');
    }
}
