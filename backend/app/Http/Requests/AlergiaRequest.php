<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AlergiaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        // Obtenemos el ID si estamos editando, si no, null
        $alergiaId = $this->route('alergia') ? $this->route('alergia')->id : null;

        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                // Usamos la regla de esta forma para evitar el error de sintaxis en SQL
                \Illuminate\Validation\Rule::unique('alergias', 'nombre')->ignore($alergiaId)
            ],
        ];
    }
}
