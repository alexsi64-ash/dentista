<?php

namespace App\Services;

use App\Models\Paciente;

class PacienteService
{
    public function listarPorNegocio($negocioId, $incluirEliminados = false) {
        $query = Paciente::where('negocio_id', $negocioId);
        if ($incluirEliminados) {
            $query->onlyTrashed();
        }
        return $query->get();
    }

    public function registrar(array $datos)
    {
        return Paciente::create($datos);
    }

    public function actualizar(Paciente $paciente, array $datos)
    {
        $paciente->update($datos);
        return $paciente;
    }

    public function eliminar(Paciente $paciente)
    {
        return $paciente->delete();
    }

    public function activar($id)
    {
        $paciente = Paciente::onlyTrashed()->findOrFail($id);
        $paciente->restore();
        return $paciente;
    }
}