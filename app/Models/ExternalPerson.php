<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExternalPerson extends Model
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
        'position',
        'company',
    ];

    /**
     * Get the documents that originated from this external person.
     * Obtener los documentos que se originaron de esta persona externa.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
