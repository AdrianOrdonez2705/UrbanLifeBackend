<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actividad sospechosa detectada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f6f6;
            color: #333;
            padding: 30px;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            padding: 25px;
            max-width: 600px;
            margin: auto;
        }
        .header {
            color: #b91c1c;
            font-weight: bold;
            font-size: 1.2em;
        }
        .details {
            margin-top: 15px;
            background: #f9fafb;
            border-left: 4px solid #f87171;
            padding: 10px 15px;
        }
        .footer {
            margin-top: 20px;
            font-size: 0.85em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="card">
        <p class="header">⚠️ Actividad sospechosa detectada</p>
        <p>Hola <strong>{{ $usuario['nombre'] ?? 'usuario' }}</strong>,</p>

        <p>Hemos detectado un intento de inicio de sesión o actividad inusual en tu cuenta.</p>

        <div class="details">
            <p><strong>Fecha:</strong> {{ $actividad['fecha'] ?? now()->format('d/m/Y H:i') }}</p>
            <p><strong>IP:</strong> {{ $actividad['ip'] ?? 'No disponible' }}</p>
            <p><strong>Ubicación estimada:</strong> {{ $actividad['ubicacion'] ?? 'Desconocida' }}</p>
            <p><strong>Dispositivo:</strong> {{ $actividad['user_agent'] ?? 'No identificado' }}</p>
        </div>

        <p>Si fuiste tú, no es necesario hacer nada.  
        Si no reconoces esta actividad, te recomendamos cambiar tu contraseña inmediatamente.</p>

        <p class="footer">
            — El equipo de seguridad de {{ config('app.name') }}
        </p>
    </div>
</body>
</html>
