<?php

use App\Http\Controllers\MensajeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PruebaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\API\AuthController;

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

// Public route for logging in.
Route::post('login', [AuthController::class, 'login']);
Route::post('registrar_usuario', [AuthController::class, 'register']);
Route::post('verify-2fa', [AuthController::class, 'verify2FA']);

Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// A protected route group for testing your token.
// This middleware uses the 'api' guard we defined in config/auth.php.
Route::middleware('auth:api')->group(function () {
    Route::get('profile', [AuthController::class, 'profile']);
});

use Illuminate\Support\Facades\Hash;

Route::get('/hash-password/{password}', function ($password) {
    return response()->json([
        'plain_text' => $password,
        'hash' => Hash::make($password)
    ]);
});
