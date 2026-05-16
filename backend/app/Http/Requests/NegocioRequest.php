<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NegocioRequest extends FormRequest
{
    public function authorize(): bool
    {
        // El control de quién puede hacerlo lo dejaremos a la Policy
        return true; 
    }

    public function rules(): array
    {
        // Obtenemos el ID del negocio de forma segura para la excepción del unique
        $negocio = $this->route('negocio') ?? $this->user()->negocio;
        $negocioId = $negocio ? $negocio->id : null;

        return [
            'nombre'         => 'required|string|max:100|unique:negocios,nombre,' . $negocioId,
            'nit'            => 'nullable|string|max:20',
            'direccion'      => 'nullable|string|max:255',
            'telefono'       => 'nullable|string|max:20',
            'url_externa'    => 'nullable|url|max:255',
            'logo'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        
            // FALTABAN ESTOS: Si no están aquí, validated() los ignora
            'tema_id'        => 'nullable|exists:temas,id',
            'tipografia'     => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del negocio es obligatorio.',
            'nombre.unique'   => 'Ya existe un negocio registrado con ese nombre.',
            'url_externa.url' => 'El formato del enlace externo no es válido.',
            'logo.image'      => 'El archivo debe ser una imagen.',
        ];
    }
}
