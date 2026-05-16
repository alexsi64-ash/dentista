<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venta;

class VentaPolicy
{
    public function viewAny(User $user)
    {
        return true; // O filtrar por rol
    }

    public function view(User $user, Venta $venta)
    {
        return $user->negocio_id === $venta->negocio_id;
    }
    
    // El método store se valida usualmente por el negocio_id en el controller
}