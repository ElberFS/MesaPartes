<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
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
        'subject',
        'summary',
        'file_id',
        // Campos de destino detallados
        'destination_type',
        'destination_office_id',
        'destination_organization_name',
        'destination_contact_person',
        'destination_contact_role',
        // Fin campos de destino
        'is_final_response',
        'date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'is_final_response' => 'boolean',
    ];

    /**
     * Get the document that the response belongs to.
     * Obtener el documento al que pertenece esta respuesta.
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Get the file associated with the response.
     * Obtener el archivo asociado a la respuesta.
     */
    public function file()
    {
        return $this->belongsTo(File::class);
    }

    /**
     * Get the office that is the destination of the response (if internal destination).
     * Obtener la oficina que es el destino de esta respuesta (si el destino es interno).
     */
    public function destinationOffice()
    {
        return $this->belongsTo(Office::class, 'destination_office_id');
    }

    /**
     * Get the follow-ups for the response.
     * Obtener los seguimientos para esta respuesta.
     */
    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }
}
