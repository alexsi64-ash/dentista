<?php

namespace App\Services;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Consulta;
use Illuminate\Support\Facades\DB;

class VentaService
{
    public function crearVenta(array $data, int $userId, int $negocioId)
    {
        return DB::transaction(function () use ($data, $userId, $negocioId) {
            
            // 1. Calcular el total (evitamos confiar ciegamente en el total que mande el front)
            $total = collect($data['detalles'])->reduce(function ($carry, $item) {
                return $carry + ($item['precio_unitario'] * $item['cantidad']);
            }, 0);

            // 2. Crear cabecera de venta
            $venta = Venta::create([
                'negocio_id'  => $negocioId,
                'paciente_id' => $data['paciente_id'],
                'user_id'     => $userId,
                'total'       => $total,
                'metodo_pago' => $data['metodo_pago'],
                'fecha_venta' => now(),
            ]);

            // 3. Procesar detalles y actualizar estados de consultas
            foreach ($data['detalles'] as $detalle) {
                VentaDetalle::create([
                    'venta_id'        => $venta->id,
                    'servicio_id'     => $detalle['servicio_id'],
                    'cantidad'        => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal'        => $detalle['precio_unitario'] * $detalle['cantidad'],
                ]);

                // Si este detalle viene de una consulta clínica, la marcamos como pagada
                if (!empty($detalle['consulta_id'])) {
                    Consulta::where('id', $detalle['consulta_id'])->update([
                        'pagado' => true,
                        'venta_id' => $venta->id // Relación inversa para auditoría
                    ]);
                }
            }

            return $venta->load('detalles.servicio');
        });
    }

    
    public function anularVenta(Venta $venta)
    {
        return DB::transaction(function () use ($venta) {
            // 1. Buscamos todas las consultas vinculadas a esta venta
            // y las marcamos nuevamente como NO pagadas.
            Consulta::where('venta_id', $venta->id)->update([
                'pagado' => false,
                'venta_id' => null
            ]);

            // 2. Aplicamos Soft Delete a la venta
            $venta->delete();

            return true;
        });
    }

    public function restaurarVenta(int $id)
    {
        return DB::transaction(function () use ($id) {
            $venta = Venta::onlyTrashed()->findOrFail($id);

            // 1. Volvemos a marcar las consultas como pagadas
            // (Nota: Esto asume que el paciente no ha pagado por otro lado en el intermedio)
            Consulta::whereHas('servicio', function($q) use ($venta) {
                $q->whereIn('servicio_id', $venta->detalles->pluck('servicio_id'));
            })
            ->where('paciente_id', $venta->paciente_id)
            ->where('pagado', false)
            ->update([
                'pagado' => true,
                'venta_id' => $venta->id
            ]);

            $venta->restore();
            return $venta;
        });
    }
}