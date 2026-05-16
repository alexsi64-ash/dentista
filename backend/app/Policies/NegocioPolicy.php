<?php

namespace App\Policies;

use App\Models\Negocio;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NegocioPolicy
{
    public function before(User $auth, string $ability)
{
    if ($auth->hasPermissionTo('total')) return true;
}

public function view(User $auth, Negocio $negocio): bool
{
    return $auth->negocio_id === $negocio->id;
}

public function update(User $auth, Negocio $negocio): bool
{
    return $auth->hasRole('admin_negocio') && $auth->negocio_id === $negocio->id;
}

// create y delete no necesitan más lógica porque el 'before' ya autoriza al Maestro
// y por defecto Laravel retornará false para los demás.
}
