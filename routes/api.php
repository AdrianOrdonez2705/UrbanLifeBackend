<?php

use App\Http\Controllers\MensajeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ContabilidadController;
use App\Http\Controllers\MaterialProveedorController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\UsuarioController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/health', function () {
    return response()->json(['status' => 'healthy']);
});

Route::get('/get_proyectos', [ProyectoController::class, 'index']);
Route::post('/mensaje', [MensajeController::class, 'store']);
Route::post('login', [AuthController::class, 'login']);
Route::post('verify-2fa', [AuthController::class, 'verify2FA']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:api')->group(function () {
    Route::get('profile', [AuthController::class, 'profile']);
});

// USUARIO
Route::post('/asignar_usuario', [UsuarioController::class, 'asignar_usuario']);
Route::get('/get_all_usuarios', [UsuarioController::class, 'index']);
Route::get('/get_usuario/id/{id_usuario}', [UsuarioController::class, 'show']);
Route::put('/update_usuario/{id_usuario}', [UsuarioController::class, 'update']);
Route::put('/eliminar_empleado/{id_empleado}', [UsuarioController::class, 'borradoLogico']);

// EMPLEADO
Route::post('/registrar_empleado', [EmpleadoController::class, 'register']);
Route::get('/get_all_empleados', [EmpleadoController::class, 'index']); // Este endpoint devuelve usuario true o false
Route::get('/get_empleado/id/{id_empleado}', [EmpleadoController::class, 'show']);
Route::put('/update_empleado/{id_empleado}', [EmpleadoController::class, 'update']);
Route::put('eliminar_empleado/{id_empleado}', [EmpleadoController::class, 'borradoLogico']);

// TRABAJADOR
Route::get('/get_all_trabajadores', [TrabajadorController::class, 'index']);
Route::post('/registrar_trabajador', [TrabajadorController::class, 'register']);
Route::put('/update_trabajador/{id_trabajador}', [TrabajadorController::class, 'update']);

// PROVEEDOR
Route::get('/get_proveedores', [ProveedorController::class, 'index']);
Route::post('/registrar_proveedor', [ProveedorController::class, 'register']);
Route::put('/update_proveedor/{id_proveedor}', [ProveedorController::class, 'update']);

// MATERIAL_PROVEEDOR
Route::get('/get_material_proveedores', [MaterialProveedorController::class, 'index']);
Route::post('/registrar_material_proveedor', [MaterialProveedorController::class, 'register']);
Route::put('/update_material_proveedor/{id_material}', [MaterialProveedorController::class, 'update']);

// PEDIDOS
Route::get('/get_material_pedidos', [PedidoController::class, 'getMaterialPedidos']);

Route::get('/get_detalles_trabajador/{id_trabajador}', [TrabajadorController::class, 'getDetallesTrabajador']);
Route::get('/get_movimientos_proyectos', [ContabilidadController::class, 'getAllMovimientos']);