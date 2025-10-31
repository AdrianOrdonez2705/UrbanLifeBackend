<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use Illuminate\Support\Facades\Mail;
//use App\Mail\Test;

/*Route::get('/probar-correo', function(){
    $usuario = (object)['nombre'=>'nombre', 'email' => 'ejemplo@gmail.com'];
    Mail::to($usuario->email)->send(new Test($usuario));

    return 'Correo enviado';
});*/

use App\Mail\ActividadSospechosa;

Route::get('/actividad_sospechosa', function(){
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

    Mail::to($user->email)->send(new RecuperarContrasenia(
        ['nombre' => $user->name, 'email' => $user->email],
        $resetUrl
    ));

    Mail::to($user-email)->send(new Verificacion2Pasos(
        ['nombre' => $user->name, 'email' => $user->email],
        $codigo
    ));
