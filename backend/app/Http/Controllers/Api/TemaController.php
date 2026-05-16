<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tema;
use App\Models\Negocio;
use Illuminate\Http\Request;

class TemaController extends Controller
{
    /**
     * Listar todos los temas base disponibles
     */
    public function index()
    {
        return response()->json(Tema::all());
    }

    /**
     * Obtener el tema y tipografía actual del negocio logueado
     */
    public function current(Request $request)
    {
        $negocio = $request->user()->negocio;
        
        // Obtenemos el tema que tenga 'activo' => true en la pivote
        $configuracion = $negocio->temas()->wherePivot('activo', true)->first();

        return response()->json([
            'tema' => $configuracion,
            'tipografia' => $configuracion ? $configuracion->pivot->tipografia : 'Inter'
        ]);
    }

    /**
     * Guardar o actualizar la configuración visual
     */
    public function updateConfig(Request $request)
    {
        $request->validate([
            'tema_id' => 'required|exists:temas,id',
            'tipografia' => 'required|string'
        ]);

        $negocio = $request->user()->negocio;

        // Desactivamos cualquier tema anterior
        $negocio->temas()->updateExistingPivot($negocio->temas->pluck('id'), ['activo' => false]);

        // Sincronizamos el nuevo tema. Si ya existía en la pivote, lo actualiza; si no, lo crea.
        // 'syncWithoutDetaching' es útil para no borrar el historial de temas usados.
        $negocio->temas()->syncWithoutDetaching([
            $request->tema_id => [
                'tipografia' => $request->tipografia,
                'activo' => true,
                'updated_at' => now()
            ]
        ]);

        return response()->json([
            'mensaje' => 'Apariencia actualizada correctamente',
            'data' => $negocio->load('temas')
        ]);
    }
}