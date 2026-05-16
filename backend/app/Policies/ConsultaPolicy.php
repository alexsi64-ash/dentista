<?php

namespace App\Policies;

use App\Models\Consulta;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ConsultaPolicy
{
    
    public function before(User $auth, string $ability)
    {
        if ($auth->hasPermissionTo('total')) return true;
    }

    public function viewAny(User $auth): bool { return true; }

    public function update(User $auth, Consulta $consulta): bool
    {
        // Solo el especialista que atendió o el admin pueden editar
        return $auth->id === $consulta->especialista_id || $auth->hasRole('admin_negocio');
    }
}
