<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ServicioRequest extends FormRequest
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
            'negocio_id' => $this->user()->negocio_id, // Laravel ya lo sabe por el token
        ]);
    }

    public function rules(): array
    {
        return [
            'negocio_id'    => 'required|exists:negocios,id',
            'nombre'        => 'required|string|max:150',
            'descripcion'   => 'nullable|string',
            'precio_base'   => 'required|numeric|min:0',
            'estado'        => 'boolean',
            // Puede ser un archivo O una cadena de texto (URL)
            'imagen_landing' => 'nullable', 
            'es_url_externa' => 'boolean' // Un pequeño flag para saber qué envió el usuario
        ];
    }
}
