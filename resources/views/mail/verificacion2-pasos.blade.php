<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación de dos pasos</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f6f6; padding: 30px; }
        .card { background: #fff; padding: 25px; border-radius: 8px; max-width: 600px; margin: auto; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .code { font-size: 28px; font-weight: bold; letter-spacing: 4px; color: #2563eb; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Hola {{ $usuario['nombre'] ?? 'usuario' }},</h2>
        <p>Para completar tu inicio de sesión, introduce el siguiente código en la aplicación:</p>
        <p class="code">{{ $codigo }}</p>
        <p>Este código expirará en 10 minutos.</p>
        <p>Si no fuiste tú quien intentó iniciar sesión, cambia tu contraseña de inmediato.</p>
        <p style="font-size: 0.9em; color: #666;">— El equipo de {{ config('app.name') }}</p>
    </div>
</body>
</html>