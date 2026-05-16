<?php

namespace App\Services;

use App\Models\Alergia;
use App\Models\Paciente;

class AlergiaService
{
    public function listarTodas($verPapelera = false)
    {
        $query = Alergia::orderBy('nombre');

        if ($verPapelera) {
            // Esto trae SOLO los que tienen deleted_at cargado
            return $query->onlyTrashed()->get();
        }

        // Esto trae SOLO los que tienen deleted_at en NULL
        return $query->get();
    }

    public function registrar(array $datos)
    {
        return Alergia::create($datos);
    }

    public function eliminar(Alergia $alergia)
    {
        return $alergia->delete();
    }

    public function restaurar($id)
    {
        $alergia = Alergia::onlyTrashed()->findOrFail($id);
        $alergia->restore();
        return $alergia;
    }

    public function sincronizarConPaciente(Paciente $paciente, array $alergiaIds)
    {
        return $paciente->alergias()->sync($alergiaIds);
    }
}