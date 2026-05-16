<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PacienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { 
        return true; 
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'negocio_id' => $this->user()->negocio_id,
            'estado' => $this->estado ?? true,
        ]);
    }

    public function rules(): array
    {
        // Obtenemos el ID del parámetro de la ruta 'paciente'
        // Usamos 'id' si es un objeto (Route Model Binding) o el valor directo si es un string
        $paciente = $this->route('paciente');
        $pacienteId = is_object($paciente) ? $paciente->id : $paciente;

        return [
            'negocio_id'       => 'required|exists:negocios,id',
            // Si $pacienteId existe, lo añadimos a la regla unique para ignorarlo, 
            // si no, pasamos NULL explícito para que Laravel no rompa el SQL
            'cedula'           => [
                'required',
                'string',
                'max:20',
                'unique:pacientes,cedula,' . ($pacienteId ?? 'NULL') . ',id'
            ],
            'nombre'           => 'required|string|max:100',
            'apellidos'        => 'required|string|max:100',
            'telefono'         => 'nullable|string|max:20',
            'fecha_nacimiento' => 'nullable|date',
            'estado'           => 'boolean'
        ];
    }
}
