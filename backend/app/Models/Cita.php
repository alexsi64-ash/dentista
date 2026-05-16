<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'citas';

    protected $fillable = [
        'negocio_id', 'paciente_id', 'servicio_id', 'nombre_cliente', 
        'telefono_cliente', 'fecha', 'hora_inicio', 'estado', 'nota_cancelacion'
    ];

    public function negocio() { return $this->belongsTo(Negocio::class); }
    public function paciente() { return $this->belongsTo(Paciente::class); }
    public function servicio() { return $this->belongsTo(Servicio::class); }
}