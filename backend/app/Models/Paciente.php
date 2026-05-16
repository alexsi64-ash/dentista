<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Model
{
    use SoftDeletes;

    protected $table = 'pacientes';

    protected $fillable = [
        'negocio_id', 
        'cedula', 
        'nombre', 
        'apellidos', 
        'telefono', 
        'fecha_nacimiento', 
        'estado'
    ];

    public function alergias() {
        return $this->belongsToMany(Alergia::class, 'alergia_paciente');
    }
    public function negocio() { return $this->belongsTo(Negocio::class); }
    public function consultas() { return $this->hasMany(Consulta::class); }
    public function ventas() { return $this->hasMany(Venta::class); }
}