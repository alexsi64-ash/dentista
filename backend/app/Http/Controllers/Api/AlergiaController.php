<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AlergiaRequest;
use App\Services\AlergiaService;
use App\Models\Alergia;
use App\Models\Paciente;
use Illuminate\Http\Request;

class AlergiaController extends Controller
{
    protected $alergiaService;

    public function __construct(AlergiaService $alergiaService)
    {
        $this->alergiaService = $alergiaService;
    }

    // CRUD del Catálogo
    public function index(Request $request)
    {
        $incluirEliminadas = $request->query('papelera') === 'true';
        return response()->json($this->alergiaService->listarTodas($incluirEliminadas));
    }

    public function store(AlergiaRequest $request)
    {
        $alergia = $this->alergiaService->registrar($request->validated());
        return response()->json(['mensaje' => 'Alergia creada', 'data' => $alergia], 201);
    }

    public function update(AlergiaRequest $request, Alergia $alergia)
    {
        // El request ya valida el nombre único ignorando el ID actual
        $alergia->update($request->validated());
        return response()->json(['mensaje' => 'Alergia actualizada', 'data' => $alergia]);
    }

    public function destroy(Alergia $alergia)
    {
        // Opcional: Validar si la alergia está siendo usada antes de borrarla
        $this->alergiaService->eliminar($alergia);
        return response()->json(['mensaje' => 'Alergia desactivada']);
    }

    public function restaurar($id)
    {
        // Usamos el ID porque el registro está "oculto" por el SoftDelete
        $alergia = $this->alergiaService->restaurar($id);
        return response()->json([
            'mensaje' => 'Alergia restaurada con éxito',
            'data' => $alergia
        ]);
    }

    // Acción para asignar alergias a un paciente
    public function asignarAPaciente(Request $request, Paciente $paciente)
    {
        $request->validate([
            'alergia_ids' => 'required|array',
            'alergia_ids.*' => 'exists:alergias,id'
        ]);

        $this->alergiaService->sincronizarConPaciente($paciente, $request->alergia_ids);
        
        return response()->json([
            'mensaje' => 'Alergias del paciente actualizadas',
            'data' => $paciente->load('alergias')
        ]);
    }
}