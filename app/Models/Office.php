<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the users for the office.
     * Obtener los usuarios que pertenecen a esta oficina.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the documents that originated from this office.
     * Obtener los documentos que se originaron en esta oficina.
     */
    public function originatedDocuments()
    {
        return $this->hasMany(Document::class, 'origin_office_id');
    }

    /**
     * Get the responses that have this office as a destination.
     * Obtener las respuestas que tienen esta oficina como destino.
     */
    public function destinedResponses()
    {
        return $this->hasMany(Response::class, 'destination_office_id');
    }

    /**
     * Get the follow-ups that are currently in this office.
     * Obtener los seguimientos que actualmente están en esta oficina.
     */
    public function currentFollowUps()
    {
        return $this->hasMany(FollowUp::class, 'current_office_id');
    }
}
