<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'level',
    ];

    /**
     * Get the documents that have this priority.
     * Obtener los documentos que tienen esta prioridad.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
