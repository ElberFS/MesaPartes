<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'subject',
        'origin_type',
        'origin_office_id',

        // Nuevos campos para origen externo (en inglés)
        'organization_name',
        'external_contact_person',
        'external_contact_role',
        'event_date',
        'event_time',

        'reference',
        'origin_in_charge',
        'summary',
        'file_id',
        'priority_id',
        'registration_date',
        'status',
        'indication', // ¡Nuevo campo añadido aquí!
    ];

    /**
     * The attributes that should be cast.
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'registration_date' => 'date',           // Convierte la fecha de registro a un objeto Carbon
        'event_date' => 'date',                  // Convierte la fecha del evento a un objeto Carbon
        'event_time' => 'datetime',              // Convierte la hora del evento a un objeto Carbon (hora sin fecha)
        // Si tienes enums de PHP 8.1+, podrías castear 'status' e 'indication' así:
        // 'status' => \App\Enums\DocumentStatus::class,
        // 'indication' => \App\Enums\DocumentIndication::class,
    ];


    /**
     * Get the office that originated the document.
     * Obtener la oficina que originó el documento.
     */
    public function originOffice()
    {
        return $this->belongsTo(Office::class, 'origin_office_id');
    }

    /**
     * Get the file associated with the document.
     * Obtener el archivo asociado al documento.
     */
    public function file()
    {
        return $this->belongsTo(File::class);
    }

    /**
     * Get the priority of the document.
     * Obtener la prioridad del documento.
     */
    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    /**
     * Get the responses for the document.
     * Obtener las respuestas para este documento.
     */
    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    /**
     * Get the follow-ups for the document.
     * Obtener los seguimientos para este documento.
     */
    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }
}
