<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PacienteController;
use App\Http\Controllers\Api\ServicioController;
use App\Http\Controllers\Api\NegocioController;
use App\Http\Controllers\Api\ConsultaController;
use App\Http\Controllers\Api\AlergiaController;
use App\Http\Controllers\Api\VentaController;
use App\Http\Controllers\Api\TemaController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('login', [AuthController::class, 'login']);

// mostrar los servicios en la landing
Route::get('servicios-publicos/{negocio_id}', [ServicioController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Requieren Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('logout', [AuthController::class, 'logout']);

    // Usuarios
    // Definimos la ruta de activar antes del resource para evitar conflictos
    Route::patch('usuarios/{id}/activar', [UserController::class, 'activar']);
    Route::apiResource('usuarios', UserController::class)->parameters([
        'usuarios' => 'usuario' // Esto asegura que el binding en el controlador sea $usuario
    ]);
    Route::get('especialistas', [UserController::class, 'especialistas']);

    // Servicios
    Route::post('servicios/{id}/activar', [ServicioController::class, 'activar']);
    Route::apiResource('servicios', ServicioController::class)->parameters([
        'servicios' => 'servicio'
    ]);

    // Negocios
    Route::post('negocios/{id}/activar', [NegocioController::class, 'activar']);
    Route::apiResource('negocios', NegocioController::class);

    // Rutas especificas del dueño de negocio
    Route::get('mi-negocio', [NegocioController::class, 'showActual']);
    Route::post('mi-negocio', [NegocioController::class, 'updateActual']);

    // Pacientes
    Route::get('pacientes/buscar', [PacienteController::class, 'buscar']); 
    Route::post('pacientes/{id}/activar', [PacienteController::class, 'activar']);
    Route::apiResource('pacientes', PacienteController::class)->parameters([
        'pacientes' => 'paciente'
    ]);
    Route::get('pacientes/{id}/historial', [PacienteController::class, 'historial']);

    // Consultas Médicas
    Route::apiResource('consultas', ConsultaController::class);
    Route::post('consultas/{id}/restaurar', [ConsultaController::class, 'activar']);

    // --- RUTAS DEL CATÁLOGO DE ALERGIAS ---
    // Listar (soporta ?papelera=true), crear, ver, actualizar y borrar
    Route::apiResource('alergias', AlergiaController::class);
    
    // Ruta para restaurar una alergia eliminada
    Route::post('alergias/{id}/restaurar', [AlergiaController::class, 'restaurar']);


    // --- RELACIÓN CON PACIENTES ---
    // Asignar alergias a un paciente específico
    Route::post('pacientes/{paciente}/alergias', [AlergiaController::class, 'asignarAPaciente']);

    // Ventas
    // Ruta específica para deudas de pacientes
    Route::get('ventas/pendientes/{paciente_id}', [VentaController::class, 'pendientes']);
    
    // Ruta para restaurar ventas anuladas
    Route::post('ventas/{id}/activar', [VentaController::class, 'activar']);
    
    // CRUD estándar
    Route::apiResource('ventas', VentaController::class);

    // Temas y Configuración Visual
    Route::get('temas', [TemaController::class, 'index']);
    Route::get('mi-negocio/apariencia', [TemaController::class, 'current']);
    Route::post('mi-negocio/apariencia', [TemaController::class, 'updateConfig']);
    
});