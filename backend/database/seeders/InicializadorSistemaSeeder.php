<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Negocio;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class InicializadorSistemaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear el Negocio Principal
        $negocio = Negocio::create([
            'nombre' => 'Plan Dental',
            'nit' => '123456789', // Puedes cambiarlo luego
            'direccion' => 'Santa Cruz, Bolivia',
            'estado' => true,
        ]);

        // 2. Configurar Roles y Permisos (Spatie)
        $roleMaestro = Role::create(['name' => 'maestro']);
        $permisoTotal = Permission::create(['name' => 'total']);
        $roleMaestro->givePermissionTo($permisoTotal);

        // Rol para el cliente (Plan Dental)
        Role::create(['name' => 'administrador']);
        Role::create(['name' => 'especialista']);
        Role::create(['name' => 'recepcion']);

        // 3. Crear tu Usuario Maestro
        // Este es el usuario que no mostrarás en los listados de la app
        User::create([
            'negocio_id' => $negocio->id,
            'cedula' => '0000000', // Tu CI real
            'nombre' => 'Alexander',
            'apellidos' => 'Silva',
            'telefono' => '70000000',
            'email' => 'alexsi@leontec.com', // Tu correo de maestro
            'password' => Hash::make('fisica01'),
            'estado' => true,
        ])->assignRole($roleMaestro);
    }
}