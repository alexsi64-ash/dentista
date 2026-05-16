<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VentaRequest;
use App\Services\VentaService;
use App\Models\Venta;
use App\Models\Consulta;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    protected $ventaService;

    public function __construct(VentaService $ventaService)
    {
        $this->ventaService = $ventaService;
    }

    // Listar ventas (con filtro para papelera/anuladas)
    public function index(Request $request)
    {
        // Filtramos por negocio y cargamos relaciones clave
        $query = Venta::with(['paciente:id,nombre,apellidos', 'cajero:id,name'])
            ->where('negocio_id', $request->user()->negocio_id);

        // Si el usuario pide ver la papelera (ventas anuladas)
        if ($request->query('papelera') === 'true') {
            $query->onlyTrashed();
        }

        return response()->json($query->latest('fecha_venta')->get());
    }

    public function store(VentaRequest $request)
    {
        $venta = $this->ventaService->crearVenta(
            $request->validated(), 
            $request->user()->id, 
            $request->user()->negocio_id
        );
        return response()->json($venta, 201);
    }

    // Ver detalle de una venta específica
    public function show($id)
    {
        // Permite ver el detalle incluso si la venta está anulada (para auditoría)
        $venta = Venta::with(['paciente', 'detalles.servicio', 'cajero'])
            ->withTrashed() 
            ->findOrFail($id);

        return response()->json($venta);
    }

    // Anular venta (Eliminación lógica)
    public function destroy(Venta $venta)
    {
        // Llamamos al servicio para que maneje la lógica de negocio
        // El Service se encargará de poner las consultas en pagado = false
        $this->ventaService->anularVenta($venta);

        return response()->json(['message' => 'Venta anulada y consultas liberadas.']);
    }

    // Restaurar venta anulada
    public function activar($id)
    {
        $venta = $this->ventaService->restaurarVenta($id);
        return response()->json(['message' => 'Venta restaurada', 'data' => $venta]);
    }

    public function pendientes($pacienteId)
    {
        return Consulta::where('paciente_id', $pacienteId)
            ->where('pagado', false)
            ->with('servicio')
            ->get();
    }
}