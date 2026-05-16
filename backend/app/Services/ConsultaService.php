<?php

namespace App\Services;

use App\Models\Consulta;
use Illuminate\Support\Facades\DB;

class ConsultaService
{
    public function listarPorNegocio($negocioId)
    {
        return Consulta::where('negocio_id', $negocioId)
            ->with(['paciente', 'especialista', 'servicio'])
            ->latest('fecha_atencion')
            ->get();
    }

    public function registrar(array $datos)
    {
        return DB::transaction(function () use ($datos) {

            if (!isset($datos['negocio_id'])) {
                $datos['negocio_id'] = auth()->user()->negocio_id;
            }

            $consulta = Consulta::create($datos);

            if (request()->hasFile('evidencias')) {
                foreach (request()->file('evidencias') as $archivo) {
                    $consulta->addMedia($archivo)->toMediaCollection('evidencias');
                }
            }

            return $consulta;
        });
    }

    public function actualizar(Consulta $consulta, array $datos)
    {
        return DB::transaction(function () use ($consulta, $datos) {
            $consulta->update($datos);

            // Si vienen nuevas imágenes, las añadimos a la colección existente
            if (isset($datos['evidencias'])) {
                foreach ($datos['evidencias'] as $archivo) {
                    $consulta->addMedia($archivo)->toMediaCollection('evidencias');
                }
            }

            return $consulta->load(['paciente', 'especialista', 'servicio']);
        });
    }

    public function eliminar(Consulta $consulta)
    {
        // Gracias al trait SoftDeletes en el modelo, esto solo llenará deleted_at
        return $consulta->delete();
    }
}