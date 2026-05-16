<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements HasMedia, Auditable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia, AuditableTrait, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'negocio_id',
        'cedula',
        'nombre',
        'apellidos',
        'telefono',
        'email',
        'password',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'estado' => 'boolean',
        ];
    }

    protected $appends = ['rol_nombre']; // Atributo virtual

    public function getRolNombreAttribute()
    {
        // Retorna el nombre del primer rol o null
        return $this->roles->first()?->name;
    }

    // Relaciones
    public function negocio() { return $this->belongsTo(Negocio::class); }
    public function consultas() { return $this->hasMany(Consulta::class, 'especialista_id'); }

    // Foto de perfil
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }
}
