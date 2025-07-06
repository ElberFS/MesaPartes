<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles; // Importar el trait de Spatie

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    // The user model requires this trait
    use HasRoles; // Usar el trait HasRoles para la gestión de roles y permisos

    /**
     * The attributes that are mass assignable.
     * Los atributos que son asignables masivamente.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'office_id', // ¡Agregado! Para permitir la asignación masiva del ID de la oficina
    ];

    /**
     * The attributes that should be hidden for serialization.
     * Los atributos que deben ocultarse para la serialización.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     * Obtener los atributos que deben ser casteados.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     * Obtener las iniciales del usuario.
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the office that the user belongs to.
     * Obtener la oficina a la que pertenece el usuario.
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

}
