<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        body {
            font-family:  Arial,Helvetica, sans-serif;
        }

        table.paleBlueRows {
  font-family: Arial, Helvetica, sans-serif;
  border: 1px solid #FDDEB1;
  width: 100%;
  text-align: center;
  border-collapse: collapse;
}
table.paleBlueRows td, table.paleBlueRows th {
  border: 1px solid #CA9558;
  padding: 3px 2px;
}
table.paleBlueRows tbody td {
  font-size: 10px;
}
table.paleBlueRows tr:nth-child(even) {
  background: #FFE0B2;
}
table.paleBlueRows thead {
  background: #E65100;
}
table.paleBlueRows thead th {
  font-size: 11px;
  font-weight: bold;
  color: #FFFFFF;
  text-align: center;
  border-left: 2px solid #E65100;
}
table.paleBlueRows thead th:first-child {
  border-left: none;
}

table.paleBlueRows tfoot td {
  font-size: 14px;
}

        .page-number:before {
            content: "Pagina "counter(page);
        }

        .text-warning {
            color: #f0ad4e !important
        }

        .text-danger {
            color: #d9534f !important
        }

        .text-success {
            color: #5cb85c !important
        }

        .text-info {
            color: #5bc0de !important
        }

        .text-primary {
            color: #0275d8 !important
        }
        .text-center{
        text-align: center
    }
    .h4,h4{
        font-size: 1rem
    }
    .h5,h5{
        font-size: 0.75rem
    }

    </style>
</head>

<body style="font-size: 11px;-webkit-text-size-adjust: none">
    <div class="px-0">
        <table width="100%" style=" font-size: 6px;">
            <tr>
                <td>
                    <table>
                        <tbody>
                            <tr>
                                <td>
                                    <div name="logo" style="vertical-align: bottom;">
                                        <img src="{{ base_path() }}/public/img/nuevologo-radical.png"
                                            style="margin-top: -25px" alt="logo">
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: bottom;text-align: right;margin-top: -25px">
                    <table width="100%" style=" font-size: 10px;">
                        <tbody>
                            <tr>
                                <td>Fecha de emisión:</td>
                                <td>{{ \Carbon\Carbon::now()->format('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <td>Emitido por:</td>
                                <td>{{ isset(\auth()->user()->name) ? \auth()->user()->name : "DEMO" }}</td>
                            </tr>
                            <td>Fecha Inicial:</td>
                            <td>{{ $personas['inicio'] }}</td>
                            </tr>
                            </tr>
                            <td>Fecha Final:</td>
                            <td>{{ $personas['fin'] }}</td>
                            </tr>

            </tbody>
        </table>
        </td>
        </tr>
        </table>

        <h4 class="h4 text-center">
            REPORTE DE ACTIVIDADES
        </h4>
        @foreach ( $personas['usuarios'] as $usuario)
        <h5>{{ $usuario['usuario'] }}</h5>
        @if (count($usuario['actividades']) > 0)
                <table class="paleBlueRows">
                    <thead>
                        <tr>
                            <th>FECHA</th>
                            <th>INICIO - FIN</th>
                            <th>TIPO</th>
                            <th>ACTIVIDAD/ES</th>
                            <th>ESTADO</th>
                            <th>VERIFICADA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ( $usuario['actividades'] as $actividad)
                        <tr>
                            <td>{{ $actividad['actividad']['fecha'] }}</td>
                            <td>{{ $actividad['h_inicio'] }} - {{ $actividad['h_fin'] }}</td>
                            <td>{{ $actividad['tipo']['descripcion'] }}</td>
                            <td>{{ $actividad['descripcion'] }}</td>
                            <td>{{ $actividad['status']['descripcion'] }}</td>
                            <td>{{ ($actividad['verificada']==0) ? 'NO':'SI' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    <p class="text-danger">No se encontraron resultados </p>
                 @endif
            @endforeach
            <br>


    </div>
    <script type="text/php">
        if (isset($pdf)) {
        $text = "Pagina {PAGE_NUM} de {PAGE_COUNT}";
        $size = 8;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 2;
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size);
    }
</script>
</body>
