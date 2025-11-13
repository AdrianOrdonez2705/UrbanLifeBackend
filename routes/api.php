<?php

use App\Http\Controllers\MensajeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PruebaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Hash;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/health', function () {
    return response()->json(['status' => 'healthy']);
});

Route::post('/prueba', [PruebaController::class, 'store']);
Route::get('/get_proveedores', [ProveedorController::class, 'index']);
Route::get('/get_proyectos', [ProyectoController::class, 'index']);
Route::post('/mensaje', [MensajeController::class, 'store']);

Route::post('login', [AuthController::class, 'login']);
Route::post('verify-2fa', [AuthController::class, 'verify2FA']);

Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:api')->group(function () {
    Route::get('profile', [AuthController::class, 'profile']);
});

Route::get('/hash-password/{password}', function ($password) {
    return response()->json([
        'plain_text' => $password,
        'hash' => Hash::make($password)
    ]);
});

Route::post('registrar_usuario', [AuthController::class, 'register']);
Route::get('/get_all_usuarios', [UsuarioController::class, 'index']);
Route::get('/get_all_usuarios/rol/{rolNombre}', [UsuarioController::class, 'findByRol']);
Route::get('/get_usuario/id/{id_usuario}', [UsuarioController::class, 'show']);
Route::get('/get_usuario/nombre/{nombre}', [UsuarioController::class, 'findByNombre']);
Route::put('/update_usuario/{id_usuario}', [UsuarioController::class, 'update']);
Route::put('/eliminar_empleado/{id_empleado}', [UsuarioController::class, 'borradoLogico']);

Route::get('/get_all_trabajadores', [TrabajadorController::class, 'index']);
Route::post('/crear_trabajador', [TrabajadorController::class, 'store']);
Route::put('/update_trabajador/{id_trabajador}', [TrabajadorController::class, 'update']);