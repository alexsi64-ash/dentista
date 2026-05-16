<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    protected function prepareForValidation()
    {
        // Esto limpia espacios extra antes de validar
        if ($this->has('nombre')) {
            $this->merge(['nombre' => trim($this->nombre)]);
        }
        if ($this->has('apellidos')) {
            $this->merge(['apellidos' => trim($this->apellidos)]);
        }
    }

    public function rules(): array
    {
        $user = $this->route('usuario');
        $userId = is_object($user) ? $user->id : $user;
        $isPost = $this->isMethod('post');

        return [
            // El negocio_id es obligatorio al crear. 
            // Al actualizar, usualmente no permitimos cambiar de negocio por seguridad.
            // 'negocio_id' => [$isPost ? 'required' : 'prohibited', 'exists:negocios,id'],
            'cedula' => [
                $isPost ? 'required' : 'sometimes',
                'string',
                'max:20',
                Rule::unique('users', 'cedula')->ignore($userId),
            ],
            'nombre' => [
                $isPost ? 'required' : 'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u', // Solo letras y espacios
            ],
            'apellidos' => [
                $isPost ? 'required' : 'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/u',
            ],
            'telefono' => 'nullable|string|max:20',
            'email' => [
                $isPost ? 'required' : 'sometimes',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => [
                $isPost ? 'required' : 'nullable',
                'min:8',
            ],
            'estado' => 'sometimes|boolean',
            'rol' => [
                $isPost ? 'required' : 'sometimes',
                'string',
                'exists:roles,name', // Si usas Spatie, esto es correcto
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.regex' => 'El nombre solo debe contener letras.',
            'apellidos.regex' => 'Los apellidos solo deben contener letras.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'email.email' => 'El formato del correo no es válido.',
            'cedula.unique' => 'Esta cédula ya existe.',
            'password.required' => 'La contraseña es obligatoria para nuevos usuarios.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            // 'negocio_id.prohibited' => 'No puedes cambiar el ID del negocio una vez creado.',
        ];
    }
}