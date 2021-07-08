<!DOCTYPE html>

<html>

<head>

    <title>Grupo Radical</title>

</head>

<body>

    <h1 class="h1">{{ $details['title'] }}</h1>

    <p>{{ $details['body'] }}</p>
    <br>
    <h3>Detalles:</h3>
    <table width="100%" border="1">
        <thead>
            <tr>
                <th>Inicio - Fin</th>
                <th>Actividades</th>
                <th>Colaboradores</th>
                <th>Observación / Resultados</th>
                <th>Completada</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($details['actividades'] as $item)
                <tr>
                    <td>{{ $item['h_inicio'] }} - {{ $item['h_fin'] }}</td>
                    <td> {{ $item['descripcion'] }}</td>
                    <td> {{ $item['colaboradores'] }}</td>
                    <td> {{ $item['observacion'] }}</td>
                    <td> {{ ($item['estado']==1) ? 'SI' : 'NO' }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
    <br>
    <br>
    <img src="https://www.gruporadical.com/build/radical/nuevologo-radical.png" alt="logo">
    <br>
    <p><small style="font-size: 10px">Este correo fue generado automaticamente, por lo cual no debe responderlo.
            Si existe algún error, eliminarlo o comunicarse con <a href="#">soporte@gruporadical.com</a></small></p>

</body>

</html>
