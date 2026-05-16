<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{

    use SoftDeletes;
    
    protected $table = 'ventas';

    protected $fillable = [
        'negocio_id', 
        'paciente_id', 
        'user_id', 
        'total', 
        'metodo_pago', 
        'fecha_venta'
    ];

    public function detalles() { 
        return $this->hasMany(VentaDetalle::class); 
    }

    public function paciente() { 
        return $this->belongsTo(Paciente::class); 
    }

    public function cajero() { 
        return $this->belongsTo(User::class, 'user_id'); 
    }
}
