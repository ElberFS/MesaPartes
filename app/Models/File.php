<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'original_name',
        'path',
        'file_type',
    ];

    /**
     * Get the document that owns this file.
     * Obtener el documento al que pertenece este archivo.
     */
    public function document()
    {
        return $this->hasOne(Document::class);
    }

    /**
     * Get the response that owns this file.
     * Obtener la respuesta a la que pertenece este archivo.
     */
    public function response()
    {
        return $this->hasOne(Response::class);
    }
}
