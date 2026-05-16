<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConsultaRequest;
use App\Services\ConsultaService;
use App\Models\Consulta;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ConsultaController extends Controller
{
    use AuthorizesRequests;

    protected $consultaService;

    public function __construct(ConsultaService $consultaService)
    {
        $this->consultaService = $consultaService;
    }

    public function index(Request $request)
    {
        $verPapelera = $request->query('papelera') === 'true';
        $consultas = $this->consultaService->listarPorNegocio($request->user()->negocio_id, $verPapelera);
        return response()->json($consultas);
    }

    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'fecha_atencion' => 'required|date',
            'atenciones' => 'required|array|min:1', // Array de servicios
            'atenciones.*.servicio_id' => 'required|exists:servicios,id',
            'atenciones.*.especialista_id' => 'required|exists:users,id',
            'atenciones.*.procedimiento' => 'required|string',
        ]);

        try {
            return \DB::transaction(function () use ($request) {
                $registros = [];

                foreach ($request->atenciones as $atencion) {
                    $consulta = Consulta::create([
                        'negocio_id' => $request->user()->negocio_id,
                        'paciente_id' => $request->paciente_id,
                        'especialista_id' => $atencion['especialista_id'],
                        'servicio_id' => $atencion['servicio_id'],
                        'procedimiento_realizado' => $atencion['procedimiento'],
                        'fecha_atencion' => $request->fecha_atencion,
                        'pagado' => false,
                    ]);

                    // Si mandas imágenes generales para toda la sesión, 
                    // o podrías asociarlas a cada servicio individualmente aquí.
                    $registros[] = $consulta;
                }

                return response()->json([
                    'mensaje' => 'Atención registrada correctamente',
                    'cantidad' => count($registros)
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al registrar: ' . $e->getMessage()], 500);
        }
    }

    public function update(ConsultaRequest $request, Consulta $consulta)
    {
        $this->authorize('update', $consulta);
    
        $actualizada = $this->consultaService->actualizar($consulta, $request->validated());
    
        return response()->json([
            'mensaje' => 'Consulta actualizada con éxito',
            'data' => $actualizada
        ]);
    }

    public function destroy(Consulta $consulta)
    {
        $this->authorize('delete', $consulta);
    
        $this->consultaService->eliminar($consulta);
    
        return response()->json([
            'mensaje' => 'Consulta enviada a la papelera (anulada)'
        ]);
    }

    public function show(Consulta $consulta)
    {
        $this->authorize('view', $consulta);
        $consulta->load(['paciente', 'especialista', 'servicio']);
        $consulta->urls_evidencias = $consulta->getMedia('evidencias')->map(fn($m) => $m->getUrl());
        return response()->json($consulta);
    }

    public function activar($id) // Método para restaurar
    {
        $activada = $this->consultaService->restaurar($id);
        return response()->json(['mensaje' => 'Consulta restaurada', 'data' => $activada]);
    }
}