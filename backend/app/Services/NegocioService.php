<?php

namespace App\Services;

use App\Models\Negocio;
use Illuminate\Support\Facades\DB;

class NegocioService
{
    /**
     * Lista todos los negocios, incluyendo los eliminados lógicamente.
     * Ideal para tu panel de Maestro.
     */
    public function listarTodos()
    {
        return Negocio::withTrashed()->latest()->get();
    }

    /**
     * Registra un nuevo negocio y procesa su logo.
     */
    public function registrar(array $datos)
    {
        return DB::transaction(function () use ($datos) {
            $negocio = Negocio::create($datos);

            if (request()->hasFile('logo')) {
                $negocio->addMediaFromRequest('logo')
                        ->toMediaCollection('logo');
            }

            return $negocio;
        });
    }

    /**
     * Actualiza los datos del negocio (nit, dirección, etc) y el logo.
     */
    public function actualizar(Negocio $negocio, array $datos)
    {
        return DB::transaction(function () use ($negocio, $datos) {
            // 1. Actualizar datos básicos del negocio
            $negocio->update($datos);

            // 2. Procesar Logo (Spatie Media Library)
            if (request()->hasFile('logo')) {
                $negocio->addMediaFromRequest('logo')
                    ->toMediaCollection('logo');
            }

            // 3. Sincronizar Tema y Tipografía (Tabla Pivote)
            // Solo si vienen en el request
            if (isset($datos['tema_id'])) {
                $negocio->temas()->sync([
                    $datos['tema_id'] => [
                        'tipografia' => $datos['tipografia'] ?? 'Inter',
                        'activo'     => true
                    ]
                ]);
            
                // Opcional: Asegurarse de que los otros temas del negocio queden como activo = false
                // (Aunque sync suele manejar esto si la lógica es de un solo tema activo)
            }

            return $negocio->load('temas'); // Cargamos la relación para devolverla al frontend
        });
    }

    /**
     * Desactivación administrativa y borrado lógico.
     */
    public function desactivar(Negocio $negocio)
    {
        return DB::transaction(function () use ($negocio) {
            // Sincronizamos el estado booleano con el soft delete
            $negocio->update(['estado' => false]);
            
            // Opcional: Podrías desactivar todos los usuarios de este negocio aquí
            // $negocio->usuarios()->update(['estado' => false]);
            
            return $negocio->delete();
        });
    }

    /**
     * Restaura un negocio de la papelera y lo marca como activo.
     */
    public function activar($id)
    {
        return DB::transaction(function () use ($id) {
            $negocio = Negocio::onlyTrashed()->findOrFail($id);
            $negocio->restore();
            $negocio->update(['estado' => true]);
            
            return $negocio;
        });
    }
}