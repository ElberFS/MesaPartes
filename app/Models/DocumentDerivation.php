<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentDerivation extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'from_office_id',
        'to_office_id',
        'sent_at',
        'status',     // enviado, recibido, atendido, de_conocimiento
        'comment',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Documento derivado
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Oficina que envía
     */
    public function fromOffice()
    {
        return $this->belongsTo(Office::class, 'from_office_id');
    }

    /**
     * Oficina que recibe
     */
    public function toOffice()
    {
        return $this->belongsTo(Office::class, 'to_office_id');
    }
}
