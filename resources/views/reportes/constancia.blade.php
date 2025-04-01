<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constancia</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .container { width: 100%; padding: 20px; }
        .header { text-align: center; font-weight: bold; font-size: 20px; }
        .sub-header { text-align: center; font-size: 16px; margin-bottom: 10px; }
        .section-title { font-weight: bold; font-size: 16px; margin-top: 20px; border-bottom: 1px solid black; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        .qr-container { text-align: center; margin-top: 20px; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>

    <div class="container">
        <!-- Encabezado -->
        <div class="header">CONSTANCIA</div>
        <div class="sub-header"> {{ optional($tramiteInfo)->nombre ?? 'Sin nombre' }}</div>

        <table>
            <tr>
                <td><strong>Nº de Trámite</strong></td>
                <td>{{ $idTramite ?? '---' }}</td>
            </tr>
            <tr>
                <td><strong>Fecha de Emisión</strong></td>
                <td>{{ date('d-m-Y') }}</td>
            </tr>
        </table>

        <!-- Datos del Titular -->
        <div class="section-title">DATOS DEL TITULAR</div>
        <table>
            <tr>
                <td><strong>Apellido y Nombre</strong></td>
                <td>{{ $detalleTramite->firstWhere('nombre', 'APELLIDO Y NOMBRE')->valor ?? '---' }}</td>
            </tr>
            <tr>
                <td><strong>CUIL/CUIT</strong></td>
                <td>{{ $detalleTramite->firstWhere('nombre', 'CUIL/CUIT')->valor ?? '---' }}</td>
            </tr>
            <tr>
                <td><strong>Nacionalidad</strong></td>
                <td>{{ $detalleTramite->firstWhere('nombre', 'NACIONALIDAD')->valor ?? '---' }}</td>
            </tr>
            <tr>
                <td><strong>Teléfono</strong></td>
                <td>{{ $detalleTramite->firstWhere('nombre', 'TELEFONO')->valor ?? '---' }}</td>
            </tr>
            <tr>
                <td><strong>DNI</strong></td>
                <td>{{ $detalleTramite->firstWhere('nombre', 'D.N.I')->valor ?? '---' }}</td>
            </tr>
        </table>

        <!-- Datos del Automotor -->
        <div class="section-title">DATOS DEL AUTOMOTOR</div>
        <table>
            <tr>
                <td><strong>Dominio</strong></td>
                <td>{{ $detalleTramite->firstWhere('nombre', 'DOMINIO')->valor ?? '---' }}</td>
            </tr>
            <tr>
                <td><strong>Marca</strong></td>
                <td>{{ $detalleTramite->firstWhere('nombre', 'MARCA')->valor ?? '---' }}</td>
            </tr>
            <tr>
                <td><strong>Modelo</strong></td>
                <td>{{ $detalleTramite->firstWhere('nombre', 'MODELO')->valor ?? '---' }}</td>
            </tr>
            <tr>
                <td><strong>Año</strong></td>
                <td>{{ $detalleTramite->firstWhere('nombre', 'AÑO')->valor ?? '---' }}</td>
            </tr>
            <tr>
                <td><strong>Color</strong></td>
                <td>{{ $detalleTramite->firstWhere('nombre', 'COLOR')->valor ?? '---' }}</td>
            </tr>
        </table>

        <!-- Motivo -->
        <div class="section-title">MOTIVO</div>
        <table>
            <tr>
                <td><strong>Motivo</strong></td>
                <td>{{ $tramiteInfo->motivo ?? '---' }}</td>
            </tr>
        </table>

        <!-- Mensaje -->
        <div class="section-title">MENSAJE</div>
        <table>
            <tr>
                <td>{{ $mensaje ?? 'Sin mensaje' }}</td>
            </tr>
        </table>

        <!-- Código QR -->
        @if(isset($qr))
            <div class="qr-container">
                <img src="data:image/png;base64,{{ $qr }}" alt="Código QR">
            </div>
        @endif

        <!-- Pie de Página -->
        <div class="footer">
            Por lo expuesto, se extiende la presente a pedido del interesado y al sólo efecto de servir como constancia de inicio de trámite.
        </div>
    </div>

</body>
</html>
