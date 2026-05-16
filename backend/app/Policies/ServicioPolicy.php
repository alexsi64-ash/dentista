<?php

namespace App\Policies;

use App\Models\Servicio;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServicioPolicy
{

    /**
     * El Maestro siempre tiene acceso total.
     */
    public function before(User $auth, string $ability)
    {
        if ($auth->hasPermissionTo('total')) return true;
    }

    /**
     * Determina quién puede ver la lista de servicios.
     * Retornamos true porque los servicios suelen ser públicos para la landing.
     */
    public function viewAny(?User $auth): bool 
    { 
        return true; 
    }

    /**
     * Determina quién puede ver un servicio específico.
     */
    public function view(?User $auth, Servicio $servicio): bool
    {
        return true;
    }

    /**
     * Determina quién puede crear servicios.
     * Solo el Maestro (vía before) o el Admin del negocio.
     */
    public function create(User $auth): bool
    {
        return $auth->hasRole('admin_negocio');
    }

    /**
     * Determina quién puede actualizar.
     */
    public function update(User $auth, Servicio $servicio): bool
    {
        // El admin solo puede editar servicios de su propio negocio
        return $auth->hasRole('admin_negocio') && $auth->negocio_id === $servicio->negocio_id;
    }

    /**
     * Determina quién puede eliminar (Soft Delete).
     */
    public function delete(User $auth, Servicio $servicio): bool
    {
        return $auth->hasRole('admin_negocio') && $auth->negocio_id === $servicio->negocio_id;
    }

    /**
     * Determina quién puede restaurar (Activar).
     */
    public function restore(User $auth, Servicio $servicio): bool
    {
        return $auth->hasRole('admin_negocio') && $auth->negocio_id === $servicio->negocio_id;
    }

    /**
     * Determina quién puede eliminar permanentemente (Normalmente solo el Maestro).
     */
    public function forceDelete(User $auth, Servicio $servicio): bool
    {
        return $auth->hasPermissionTo('total');
    }
}
