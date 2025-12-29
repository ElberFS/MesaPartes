<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expediente extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'subject',
        'description',
    ];

    /**
     * Un expediente tiene muchos documentos
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Referencias donde este expediente es origen
     */
    public function referencesFrom()
    {
        return $this->morphMany(DocumentReference::class, 'source');
    }

    /**
     * Referencias donde este expediente es destino
     */
    public function referencesTo()
    {
        return $this->morphMany(DocumentReference::class, 'target');
    }
}
