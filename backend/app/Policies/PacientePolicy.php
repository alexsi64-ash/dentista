<?php

namespace App\Policies;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PacientePolicy
{
    public function before(User $auth, string $ability)
    {
        if ($auth->hasPermissionTo('total')) return true;
    }

    public function viewAny(User $auth): bool 
    {
        return $auth->hasRole('admin_negocio') || $auth->hasRole('empleado');
    }

    public function update(User $auth, Paciente $paciente): bool
    {
        return $auth->negocio_id === $paciente->negocio_id;
    }

    public function delete(User $auth, Paciente $paciente): bool
    {
        return $auth->negocio_id === $paciente->negocio_id;
    }

    public function restore(User $auth, Paciente $paciente): bool
    {
        return $auth->negocio_id === $paciente->negocio_id;
    }
}
