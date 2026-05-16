<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alergia extends Model
{
    use SoftDeletes; // <-- Importante

    protected $fillable = ['nombre'];

    public function pacientes()
    {
        return $this->belongsToMany(Paciente::class, 'alergia_paciente');
    }
}