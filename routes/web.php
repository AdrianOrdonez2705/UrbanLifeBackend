<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use Illuminate\Support\Facades\Mail;
use App\Mail\Test;

Route::get('/probar-correo', function(){
    $usuario = (object)['nombre'=>'nombre', 'email' => 'ejemplo@gmail.com'];
    Mail::to($usuario->email)->send(new Test($usuario));

    return 'Correo enviado';
});