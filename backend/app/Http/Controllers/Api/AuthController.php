<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales son incorrectas.'],
            ]);
        }

        if (!$user->estado) {
            return response()->json(['message' => 'Tu cuenta está desactivada.'], 403);
        }

        // Generamos el token con un nombre interno fijo
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id'         => $user->id,
                'nombre'     => $user->nombre,
                'apellidos'  => $user->apellidos,
                'email'      => $user->email,
                'negocio_id' => $user->negocio_id,
                'rol'        => $user->getRoleNames()->first(),
                // Enviamos solo los nombres de los permisos para que el frontend los use fácilmente
                'permisos'   => $user->getAllPermissions()->pluck('name'), 
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}