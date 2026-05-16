<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function listarTodo($negocioId, $incluirEliminados = false)
    {
        // Iniciamos la consulta base por negocio
        $query = User::where('negocio_id', $negocioId)
                 ->with('roles')
                 ->where('id', '!=', auth()->id()); // No te muestras a ti mismo

        if ($incluirEliminados) {
            // Muestra SOLO los que están en la papelera
            $query->onlyTrashed();
        } 
        // Si $incluirEliminados es false, Laravel por defecto oculta los SoftDeleted.

        return $query->get();
    }

    public function listarEspecialistasPorNegocio($negocioId)
    {
        // Asumiendo que usas Spatie Permissions o una columna 'role'
        return User::where('negocio_id', $negocioId)
            ->where('rol', 'especialista') // Ajusta según cómo manejes los roles
            ->select('id', 'name')
            ->get();
    }

    public function registrar(array $datos)
    {
        $datos['password'] = Hash::make($datos['password']);
    
        // Aquí $datos ya incluye el negocio_id que pusimos en el controlador
        $usuario = User::create($datos);
    
        // Spatie assignRole
        $usuario->assignRole($datos['rol']);
    
        return $usuario;
    }

    public function editar(User $usuario, array $datos)
    {
        if (!empty($datos['password'])) {
            $datos['password'] = Hash::make($datos['password']);
        } else {
            unset($datos['password']);
        }

        $usuario->update($datos);
        
        if (isset($datos['rol'])) {
            $usuario->syncRoles($datos['rol']);
        }

        return $usuario;
    }

    public function eliminar(User $usuario)
    {
        return $usuario->delete();
    }

    public function activar($id)
    {
        $usuario = User::onlyTrashed()->findOrFail($id);
        $usuario->restore();
        return $usuario;
    }
}