<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tema extends Model
{
    use HasFactory;

    protected $table = 'temas';

    protected $fillable = [
        'nombre',
        'color_primario',
        'color_secundario',
        'color_fondo'
    ];

    // Relación con Negocios (Muchos a Muchos)
    public function negocios()
    {
        return $this->belongsToMany(Negocio::class, 'negocio_tema')
                    ->withPivot('tipografia', 'activo')
                    ->withTimestamps();
    }
}