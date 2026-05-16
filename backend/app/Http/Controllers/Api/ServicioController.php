<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServicioRequest;
use App\Services\ServicioService;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ServicioController extends Controller
{
    use AuthorizesRequests;

    protected $servicioService;

    public function __construct(ServicioService $servicioService)
    {
        $this->servicioService = $servicioService;
    }

    public function index(Request $request)
    {
        // Obtenemos el negocio_id del usuario o de la ruta
        $negocioId = $request->user() ? $request->user()->negocio_id : $request->route('negocio_id');
    
        // Capturamos si el usuario quiere ver la papelera (viene como string 'true')
        $verPapelera = $request->query('papelera') === 'true';

        $servicios = $this->servicioService->listarTodo($negocioId, $verPapelera);

        return response()->json($servicios);
    }

    public function store(ServicioRequest $request)
    {
        $this->authorize('create', Servicio::class);
        $servicio = $this->servicioService->registrar($request->validated());
        return response()->json(['mensaje' => 'Servicio creado', 'data' => $servicio], 201);
    }

    public function update(ServicioRequest $request, Servicio $servicio)
    {
        $this->authorize('update', $servicio);
        $actualizado = $this->servicioService->editar($servicio, $request->validated());
        return response()->json(['mensaje' => 'Servicio actualizado', 'data' => $actualizado]);
    }

    public function destroy(Servicio $servicio)
    {
        $this->authorize('delete', $servicio);
        $this->servicioService->eliminar($servicio);
        return response()->json(['mensaje' => 'Servicio desactivado']);
    }

    public function activar(Request $request, $id)
    {
        $servicio = Servicio::withTrashed()->findOrFail($id);
    
        // Aquí Laravel busca el método 'restore' en la ServicioPolicy
        $this->authorize('restore', $servicio);
    
        $activado = $this->servicioService->activar($id);
        return response()->json(['mensaje' => 'Servicio activado', 'data' => $activado]);
    }
}
