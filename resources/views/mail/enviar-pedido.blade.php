<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Hola {{ $proveedor }}</h2>
    <p>Adjunto encontrarás el pedido de materiales solicitado.</p>
    <p><strong>Fecha:</strong> {{ $date }}</p>
    <p><strong>Total:</strong> {{ number_format($total, 2) }}</p>
</body>
</html>