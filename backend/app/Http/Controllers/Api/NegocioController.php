<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NegocioRequest;
use App\Services\NegocioService;
use App\Models\Negocio;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NegocioController extends Controller
{
    use AuthorizesRequests;

    protected $negocioService;

    public function __construct(NegocioService $negocioService)
    {
        $this->negocioService = $negocioService;
    }

    public function index()
    {
        $this->authorize('viewAny', Negocio::class);
        return response()->json($this->negocioService->listarTodos());
    }

    public function store(NegocioRequest $request)
    {
        $this->authorize('create', Negocio::class);
        $negocio = $this->negocioService->registrar($request->validated());
        return response()->json(['mensaje' => 'Negocio creado con éxito', 'data' => $negocio], 201);
    }

    public function update(NegocioRequest $request, Negocio $negocio)
    {
        $this->authorize('update', $negocio);
        $actualizado = $this->negocioService->actualizar($negocio, $request->validated());
        return response()->json(['mensaje' => 'Datos actualizados', 'data' => $actualizado]);
    }

    public function destroy(Negocio $negocio)
    {
        $this->authorize('delete', $negocio);
        $this->negocioService->desactivar($negocio);
        return response()->json(['mensaje' => 'Negocio desactivado y movido a papelera']);
    }

    public function activar($id)
    {
        // Solo el Maestro puede restaurar un negocio completo
        $negocio = $this->negocioService->activar($id);
        return response()->json(['mensaje' => 'Negocio reactivado', 'data' => $negocio]);
    }

    public function show($id)
    {
        // Buscamos el negocio y cargamos el tema que esté marcado como activo en la pivote
        $negocio = Negocio::with(['temas' => function($query) {
            $query->wherePivot('activo', true);
        }])->findOrFail($id);

        return response()->json($negocio);
    }

    public function showActual(Request $request)
    {
        $negocio = $request->user()->negocio()
            ->with(['temas' => function($query) {
                $query->wherePivot('activo', true);
            }])
            ->firstOrFail(); 

        return response()->json($negocio);
    }

    public function updateActual(NegocioRequest $request)
    {
        $negocio = $request->user()->negocio;

        if (!$negocio) {
            return response()->json(['error' => 'No tienes un negocio vinculado'], 404);
        }

        // El Request ya validó todo, se lo pasamos al Service
        $actualizado = $this->negocioService->actualizar($negocio, $request->validated());

        return response()->json([
            'mensaje' => 'Configuración actualizada con éxito',
            'data' => $actualizado
        ]);
    }
}
