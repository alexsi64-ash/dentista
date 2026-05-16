<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;

class Negocio extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $table = 'negocios';

    protected $appends = ['logo_url'];

    protected $fillable = [
        'nombre', 
        'nit', 
        'direccion', 
        'telefono', 
        'estado', 
        'url_externa',
        'color_primario'  
    ];

    // Relaciones
    public function usuarios() { 
        return $this->hasMany(User::class); 
    }

    public function servicios() { 
        return $this->hasMany(Servicio::class); 
    }

    public function pacientes() { 
        return $this->hasMany(Paciente::class); 
    }

    public function citas() { 
        return $this->hasMany(Cita::class); 
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public function getLogoUrlAttribute()
    {
        return $this->hasMedia('logo') 
            ? $this->getFirstMediaUrl('logo') 
            : ($this->url_externa ?? 'https://via.placeholder.com/150?text=Logo');
    }

    // temas
    public function temas()
    {
        return $this->belongsToMany(Tema::class)
                ->withPivot('tipografia', 'activo')
                ->withTimestamps();
    }

    // Helper para obtener el tema activo fácilmente
    public function getTemaActivoAttribute()
    {
        return $this->temas()->wherePivot('activo', true)->first();
    }
}