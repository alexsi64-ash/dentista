<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Importar
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Consulta extends Model implements HasMedia, Auditable
{
    use InteractsWithMedia, AuditableTrait, SoftDeletes; // Añadir SoftDeletes

    protected $table = 'consultas';
    protected $appends = ['urls_evidencias']; // Atributo virtual para el JSON

    protected $fillable = [
        'negocio_id', 'paciente_id', 'especialista_id', 'servicio_id', 
        'observaciones', 'procedimiento_realizado', 'fecha_atencion'
    ];

    // Formateo automático de las imágenes para el Frontend
    public function getUrlsEvidenciasAttribute()
    {
        return $this->getMedia('evidencias')->map(fn($m) => $m->getUrl());
    }

    public function paciente() { return $this->belongsTo(Paciente::class); }
    public function especialista() { return $this->belongsTo(User::class, 'especialista_id'); }
    public function servicio() { return $this->belongsTo(Servicio::class); }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('evidencias');
    }
}