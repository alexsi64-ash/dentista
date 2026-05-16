<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests; // Necesario para usar $this->authorize en Laravel 11

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        // Convertimos el string 'true'/'false' de la URL a booleano real
        $incluirEliminados = $request->query('papelera') === 'true';

        $usuarios = $this->userService->listarTodo(
            $request->user()->negocio_id, 
            $incluirEliminados
        );

        return response()->json($usuarios);
    }

    public function especialistas(Request $request)
    {
        // 1. Usamos el scope de Spatie 'role'
        // 2. IMPORTANTE: En PostgreSQL, si usas 'select', debes incluir 
        // todas las columnas que Spatie necesita para la relación o 
        // hacer el select DESPUÉS de filtrar.
    
        $especialistas = User::role('especialista')
            ->where('negocio_id', $request->user()->negocio_id)
            ->get(['id', 'nombre', 'apellidos']); // Traemos los datos reales de tu tabla

        // 3. Formateamos para que el Frontend reciba "name" como espera
        $formateados = $especialistas->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->nombre . ' ' . $user->apellidos
            ];
        });

        return response()->json($formateados);
    }

    public function store(UserRequest $request)
    {
        $this->authorize('create', User::class);
    
        // Obtenemos los datos validados
        $datos = $request->validated();
    
        // Inyectamos el negocio_id del usuario autenticado
        $datos['negocio_id'] = $request->user()->negocio_id;
    
        $usuario = $this->userService->registrar($datos);
    
        return response()->json([
            'mensaje' => 'Usuario creado con éxito', 
            'data' => $usuario
        ], 201);
    }

    public function update(UserRequest $request, User $usuario)
    {
        $this->authorize('update', $usuario);
        
        $actualizado = $this->userService->editar($usuario, $request->validated());
        return response()->json(['mensaje' => 'Usuario actualizado con éxito', 'data' => $actualizado]);
    }

    public function destroy(User $usuario)
    {
        $this->authorize('delete', $usuario);
        
        $this->userService->eliminar($usuario);
        return response()->json(['mensaje' => 'Usuario movido a la papelera']);
    }

    public function activar(Request $request, $id)
    {
        // Buscamos al usuario incluso si está en la papelera para poder autorizarlo
        $usuario = User::withTrashed()->findOrFail($id);
        
        $this->authorize('restore', $usuario);
        
        $activado = $this->userService->activar($id);
        return response()->json(['mensaje' => 'Usuario activado con éxito', 'data' => $activado]);
    }
}