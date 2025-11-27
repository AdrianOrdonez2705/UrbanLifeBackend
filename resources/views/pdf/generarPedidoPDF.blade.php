<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>{{ $title }}</title>
</head>
<body>
    <center><h2>Urban Life</h2></center>
    <center><h2>Pedido Material</h2></center>
    <h2>{{$proveedor}}</h2>
    <h2>Fecha: {{ $date }}</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>N°</th>
                <th>Material</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($materiales as $item)
            <tr>
                <td>{{$loop -> iteration}}</td>
                <td>{{$item -> material}}</td>
            </tr>
            $cont++;
            @endforeach
        </tbody>
    </table>
</body>
</html>