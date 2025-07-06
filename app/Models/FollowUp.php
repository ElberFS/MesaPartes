<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'document_id',
        'response_id',
        'current_office_id',
        'observation',
        'date',
    ];

    /**
     * Get the document that the follow-up belongs to.
     * Obtener el documento al que pertenece este seguimiento.
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the response that the follow-up belongs to (if applicable).
     * Obtener la respuesta a la que pertenece este seguimiento (si aplica).
     */
    public function response()
    {
        return $this->belongsTo(Response::class);
    }

    /**
     * Get the office where the follow-up is currently located.
     * Obtener la oficina donde se encuentra actualmente este seguimiento.
     */
    public function currentOffice()
    {
        return $this->belongsTo(Office::class, 'current_office_id');
    }
}
