<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f6f6; padding: 30px; }
        .card { background: #fff; padding: 25px; border-radius: 8px; max-width: 600px; margin: auto; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .btn { display: inline-block; background: #2563eb; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Hola {{ $usuario['nombre'] ?? 'usuario' }},</h2>
        <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta.</p>
        <p>Haz clic en el siguiente botón para continuar:</p>

        <p>
            <a href="{{ $resetUrl }}" class="btn">Restablecer contraseña</a>
        </p>

        <p>Este enlace expirará en 30 minutos.</p>

        <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>

        <p style="font-size: 0.9em; color: #666;">— El equipo de {{ config('app.name') }}</p>
    </div>
</body>
</html>