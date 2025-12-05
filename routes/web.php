<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActividadSospechosa;
use App\Mail\RecuperarContrasenia;
use App\Mail\Verificacion2Pasos;
use App\Http\Controllers\PedidoController;

Route::get('generarPDF/{idPedido}',[App\Http\Controllers\PdfController::Class, 'generarPDF']);
Route::get('/reporte/{id}', [PedidoController::class, 'contabilidadPorProyecto']);

Route::get('/actividad_sospechosa', function () {
    $usuario = [
        'nombre' => 'User',
        'email' => 'adrigonzocsi@gmail.com',
    ];

    $actividad = [
        'fecha' => now()->format('d/m/Y H:i'),
        'ip' => request()->ip(),
        'ubicacion' => 'La Paz, Bolivia',
        'user_agent' => request()->userAgent(),
    ];

    Mail::to($usuario['email'])->send(new ActividadSospechosa($usuario, $actividad));

    return 'Correo de actividad sospechosa enviado';
});

Route::get('/recuperar_contrasenia', function () {
    $user = (object)['name' => 'User', 'email' => 'adrigonzocsi@gmail.com'];
    $resetUrl = url('/reset?token=123456');

    Mail::to($user->email)->send(new RecuperarContrasenia(
        ['nombre' => $user->name, 'email' => $user->email],
        $resetUrl
    ));

    return 'Correo de recuperación enviado';
});

Route::get('/verificacion_2pasos', function () {
    $user = (object)['name' => 'User', 'email' => 'adrigonzocsi@gmail.com'];
    $codigo = '123456';

    Mail::to($user->email)->send(new Verificacion2Pasos(
        ['nombre' => $user->name, 'email' => $user->email],
        $codigo
    ));

    return 'Correo de verificación de 2 pasos enviado';
});
