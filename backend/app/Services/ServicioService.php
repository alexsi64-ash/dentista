<?php

namespace App\Services;

use App\Models\Servicio;

class ServicioService
{
    public function listarTodo($negocioId, $verPapelera = false)
    {
        $query = Servicio::where('negocio_id', $negocioId);

        if ($verPapelera) {
            // Retornamos SOLO los eliminados
            return $query->onlyTrashed()->get();
        }

        // Retornamos los normales (activos)
        return $query->get();
    }

    // Ejemplo de lógica para obtener la imagen final
    public function obtenerImagen(Servicio $servicio)
    {
        // 1. Prioridad: Imagen cargada físicamente (Spatie)
        if ($servicio->hasMedia('portada_landing')) {
            return $servicio->getFirstMediaUrl('portada_landing');
        }

        // 2. Secundaria: URL externa
        if ($servicio->url_externa) {
            return $servicio->url_externa;
        }

        // 3. Fallback: Imagen por defecto
        return asset('images/default-thumbnail.png');
    }

    public function registrar(array $datos)
    {
        // Separamos la imagen de los datos principales
        $imagen = $datos['imagen_landing'] ?? null;
        unset($datos['imagen_landing']);

        $servicio = Servicio::create($datos);

        if ($imagen) {
            if (request()->hasFile('imagen_landing')) {
                // Caso 1: Archivo subido
                $servicio->addMediaFromRequest('imagen_landing')->toMediaCollection('portada_landing');
                $servicio->update(['url_externa' => null]); // Limpiamos la URL si había una
            } else {
                // Caso 2: Es una URL de texto
                $servicio->update(['url_externa' => $imagen]);
            }
        }

        return $servicio;
    }

    public function editar(Servicio $servicio, array $datos)
    {
        $servicio->update($datos);

        if (request()->hasFile('imagen_landing')) {
            $servicio->addMediaFromRequest('imagen_landing')->toMediaCollection('portada_landing');
        }

        return $servicio;
    }

    public function eliminar(Servicio $servicio)
    {
        return $servicio->delete();
    }

    public function activar($id)
    {
        $servicio = Servicio::onlyTrashed()->findOrFail($id);
        $servicio->restore();
        return $servicio;
    }
}