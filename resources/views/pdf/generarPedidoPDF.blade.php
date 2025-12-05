<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Material</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        h2, h3 {
            text-align: center;
            margin: 10px 0;
        }

        .info {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            padding: 0 10px;
        }

        .card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #0D580D;
            color: #fff;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .total {
            text-align: right;
            margin-top: 20px;
            font-size: 1.2em;
            font-weight: bold;
        }
        .logo {
            position: absolute;
            top: 20px;   /* distancia desde arriba */
            left: 20px;  /* distancia desde la izquierda */
            width: 100px; /* tamaño de la imagen */
        }

        .header {
            text-align: center;
            margin-top: 40px; /* deja espacio para el logo */
        }

    </style>
</head>
<body>
    <img src="{{ public_path('images/urbanlogo.png') }}" class="logo" alt="Logo">

    <h2>Urban Life</h2>
    <h2>Pedido Material</h2>

    <div class="info">
        <div>
            <strong>Proveedor:</strong> {{ $proveedor }}
        </div>
        <div>
            <strong>Fecha:</strong> {{ $date }}
        </div>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pedido->materiales_pedido as $item)
                <tr>
                    <td>{{ $item->materialProveedor->material }}</td>
                    <td>{{ $item->cantidad }}</td>
                    <td>{{ number_format($item->precio_unitario, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="total">
        Monto total: {{ number_format($total, 2) }}
    </div>
</body>
</html>