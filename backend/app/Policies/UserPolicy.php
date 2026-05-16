<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{

    public function before(User $auth, string $ability)
    {
        if ($auth->hasPermissionTo('total')) {
            return true; // El Maestro salta todas las validaciones
        }
    }

    /**
     * ¿Quién puede ver la lista de usuarios?
     */
    public function viewAny(User $auth): bool
    {
        // El Maestro ve todo. El admin de negocio ve su lista.
        return $auth->hasPermissionTo('total') || $auth->hasRole('admin_negocio');
    }

    /**
     * ¿Quién puede crear nuevos usuarios?
     */
    public function create(User $auth): bool
    {
        // Importante: Cambiamos a true para que el Maestro y el Admin puedan crear
        return $auth->hasPermissionTo('total') || $auth->hasRole('admin_negocio');
    }

    /**
     * ¿Quién puede editar a un usuario específico?
     */
    public function update(User $auth, User $target): bool
    {
        // 1. Si es Maestro, adelante.
        if ($auth->hasPermissionTo('total')) return true;

        // 2. Si es Admin, solo si el usuario pertenece a su mismo negocio.
        return $auth->hasRole('admin_negocio') && ($auth->negocio_id === $target->negocio_id);
    }

    /**
     * ¿Quién puede eliminar (Soft Delete)?
     */
    public function delete(User $auth, User $target): bool
    {
        // 1. Evitar que alguien se borre a sí mismo
        if ($auth->id === $target->id) return false;

        // 2. Maestro tiene permiso total
        if ($auth->hasPermissionTo('total')) return true;

        // 3. Admin solo dentro de su negocio
        return $auth->hasRole('admin_negocio') && ($auth->negocio_id === $target->negocio_id);
    }

    /**
     * ¿Quién puede restaurar (Activar) a un usuario eliminado?
     */
    public function restore(User $auth, User $target): bool
    {
        // Usamos la misma lógica que el update
        if ($auth->hasPermissionTo('total')) return true;
        return $auth->hasRole('admin_negocio') && ($auth->negocio_id === $target->negocio_id);
    }
}
