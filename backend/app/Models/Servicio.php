<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $appends = ['imagen_final'];

    protected $table = 'servicios';

    protected $fillable = ['negocio_id', 'nombre', 'descripcion', 'precio_base', 'estado'];

    public function getImagenFinalAttribute()
    {
        // 1. Prioridad: Imagen cargada físicamente (Spatie)
        if ($this->hasMedia('portada_landing')) {
            return $this->getFirstMediaUrl('portada_landing');
        }

        // 2. Secundaria: URL externa
        if ($this->url_externa) {
            return $this->url_externa;
        }

        // 3. Fallback: Imagen por defecto (puedes usar una de una CDN)
        return 'https://via.placeholder.com/400x300?text=Sin+Imagen';
    }
    
    public function negocio() { 
        return $this->belongsTo(Negocio::class); 
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('portada_landing')->singleFile();
    }
}