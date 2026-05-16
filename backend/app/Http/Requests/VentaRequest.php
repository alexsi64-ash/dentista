<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VentaRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'paciente_id' => 'required|exists:pacientes,id',
            'metodo_pago' => 'required|in:Efectivo,Transferencia,QR,Tarjeta',
            'detalles' => 'required|array|min:1',
            'detalles.*.servicio_id' => 'required|exists:servicios,id',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.consulta_id' => 'nullable|exists:consultas,id'
        ];
    }
}