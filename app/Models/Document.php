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
        'external_person_id',
        'reference',
        'origin_in_charge',
        'summary',
        'file_id',
        'priority_id',
        'registration_date',
        'status',
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
     * Get the external person that originated the document.
     * Obtener la persona externa que originó el documento.
     */
    public function externalPerson()
    {
        return $this->belongsTo(ExternalPerson::class);
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
