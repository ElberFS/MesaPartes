<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    /**
     * Atributos asignables
     */
    protected $fillable = [
        'name',
        'acronym',
        'is_active',
    ];

    /**
     * Usuarios que pertenecen a la oficina
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'office_user')
            ->withPivot(['is_boss', 'assigned_at'])
            ->withTimestamps();
    }

    /**
     * Jefes de la oficina
     */
    public function bosses()
    {
        return $this->users()->wherePivot('is_boss', true);
    }
}
