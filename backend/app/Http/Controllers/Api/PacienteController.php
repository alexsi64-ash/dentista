<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PacienteRequest;
use App\Services\PacienteService;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PacienteController extends Controller
{
    use AuthorizesRequests;

    protected $pacienteService;

    public function __construct(PacienteService $pacienteService)
    {
        $this->pacienteService = $pacienteService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Paciente::class);
        $incluirEliminados = $request->query('papelera') == 'true';
        $pacientes = $this->pacienteService->listarPorNegocio($request->user()->negocio_id, $incluirEliminados);
        return response()->json($pacientes);
    }

    public function buscar(Request $request)
    {
        $query = $request->get('q');
    
        $pacientes = Paciente::where('negocio_id', $request->user()->negocio_id)
            ->where(function($q) use ($query) {
                $q->where('nombre', 'ILIKE', "%{$query}%")
                    ->orWhere('apellidos', 'ILIKE', "%{$query}%")
                    ->orWhere('cedula', 'ILIKE', "%{$query}%");
            })
            ->limit(20)
            ->get()
            ->map(function ($paciente) {
                return [
                    'id' => $paciente->id,
                    // Concatenamos aquí para que el frontend no tenga que hacerlo
                    'full_name' => $paciente->nombre . ' ' . $paciente->apellidos,
                    'cedula' => $paciente->cedula ?? 'Sin CI', 
                ];
            });

        return response()->json($pacientes);
    }

    public function historial($id)
    {
        // Buscamos todas las consultas del paciente
        $historial = Consulta::where('paciente_id', $id)
            ->with(['servicio', 'especialista']) // Cargamos relaciones
            ->orderBy('fecha_atencion', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($historial);
    }

    public function store(PacienteRequest $request)
    {
        $paciente = $this->pacienteService->registrar($request->validated());
        return response()->json(['mensaje' => 'Paciente registrado', 'data' => $paciente], 201);
    }

    public function update(PacienteRequest $request, Paciente $paciente)
    {
        $this->authorize('update', $paciente);
        $actualizado = $this->pacienteService->actualizar($paciente, $request->validated());
        return response()->json(['mensaje' => 'Datos actualizados', 'data' => $actualizado]);
    }

    public function destroy(Paciente $paciente)
    {
        $this->authorize('delete', $paciente);
        $this->pacienteService->eliminar($paciente);
        return response()->json(['mensaje' => 'Paciente enviado a la papelera']);
    }

    public function activar($id)
    {
        $paciente = Paciente::withTrashed()->findOrFail($id);
        $this->authorize('restore', $paciente);
        $activado = $this->pacienteService->activar($id);
        return response()->json(['mensaje' => 'Paciente reactivado', 'data' => $activado]);
    }
}