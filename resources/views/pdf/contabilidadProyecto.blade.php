<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        h2, h3 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #333; color: #fff; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .total { text-align: right; margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Reporte de Contabilidad</h2>
    <h3>Proyecto: {{ $proyecto->nombre }}</h3>
    <p><strong>Fecha de generación:</strong> {{ $date }}</p>

    <table>
        <thead>
            <tr>
                <th>ID Pedido</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Tipo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pedidos as $pedido)
                @if($pedido->contabilidad)
                <tr>
                    <td>{{ $pedido->id_pedido }}</td>
                    <td>{{ $pedido->contabilidad->descripcion }}</td>
                    <td>{{ $pedido->contabilidad->fecha }}</td>
                    <td>{{ number_format($pedido->contabilidad->monto, 2) }}</td>
                    <td>{{ $pedido->contabilidad->tipo }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total del Proyecto: {{ number_format($totalProyecto, 2) }}
    </div>
</body>
</html>