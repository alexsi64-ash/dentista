<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ConsultaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { return true; }

    protected function prepareForValidation()
    {
        // Inyectamos los valores antes de validar
        $this->merge([
            'negocio_id' => $this->user()->negocio_id,
        ]);
    }

    public function rules(): array
    {
        return [
            'paciente_id'             => 'required|exists:pacientes,id',
            'especialista_id'        => 'required|exists:users,id',
            'servicio_id'            => 'required|exists:servicios,id',
            'observaciones'          => 'nullable|string',
            'procedimiento_realizado' => 'required|string',
            'fecha_atencion'         => 'required|date',
            // Validamos que si hay evidencias, sean imágenes válidas
            'evidencias'             => 'nullable|array',
            'evidencias.*'           => 'image|mimes:jpg,jpeg,png|max:5120', 
        ];
    }
}
