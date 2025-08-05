<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constancia de Trámite</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .container { width: 100%; max-width: 700px; margin: 0 auto; }
        .title { text-align: center; font-size: 18px; font-weight: bold; }
        .subtitle { text-align: center; font-size: 14px; margin-bottom: 20px; }
        .section { margin-bottom: 10px; font-size: 12px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table td, .table th { border: 1px solid #000; padding: 8px; }
        .table th { background-color: #f2f2f2; text-align: left; }
        .qr-container { text-align: center; margin-top: 20px; }
        .qr-container img { width: 150px; }
    </style>
</head>
<body>
    <div class="container">
        <p class="title">{{ $titulo }}</p>
        <p class="subtitle">{{ $subtitulo }}</p>

        <table class="table">
            <tr>
                <th>Nº de Trámite</th>
                <td>{{ $id_tramite }}</td>
            </tr>
            <tr>
                <th>Fecha de Emisión</th>
                <td>{{ $fecha_emision }}</td>
            </tr>
            <tr>
                <th>CUIT</th>
                <td>{{ $cuit }}</td>
            </tr>
            <tr>
                <th>Razón Social</th>
                <td>{{ $razon_social }}</td>
            </tr>
        </table>

        <p class="section"><strong>DATOS DEL VEHÍCULO</strong></p>
        <table class="table">
            <tr>
                <th>Dominio</th>
                <td>{{ $dominio }}</td>
            </tr>
            <tr>
                <th>Marca</th>
                <td>{{ $marca }}</td>
            </tr>
            <tr>
                <th>Modelo</th>
                <td>{{ $modelo }}</td>
            </tr>
            <tr>
                <th>Año</th>
                <td>{{ $anio }}</td>
            </tr>
            <tr>
                <th>Color</th>
                <td>{{ $color }}</td>
            </tr>
        </table>

        <p class="section"><strong>DATOS DEL TITULAR</strong></p>
        <table class="table">
            <tr>
                <th>Apellido y Nombre</th>
                <td>{{ $razon_social }}</td>
            </tr>
            <tr>
                <th>DNI</th>
                <td>{{ $dni }}</td>
            </tr>
            <tr>
                <th>CUIL/CUIT</th>
                <td>{{ $cuit }}</td>
            </tr>
            <tr>
                <th>Nacionalidad</th>
                <td>{{ $nacionalidad }}</td>
            </tr>
            <tr>
                <th>Teléfono</th>
                <td>{{ $telefono }}</td>
            </tr>
        </table>

        <p class="section"><strong>MOTIVO</strong></p>
        <table class="table">
            <tr>
                <th>Motivo</th>
                <td>{{ $motivo }}</td>
            </tr>
            <tr>
                <th>Mensaje</th>
                <td>{{ $mensaje }}</td>
            </tr>
        </table>

        <p style="text-align: center; margin-top: 20px;">Por lo expuesto, se extiende la presente a pedido del interesado y al sólo efecto de servir como constancia de inicio de trámite.</p>

        <div class="qr-container">
            <p>Escanea el código QR para ver el trámite:</p>
            <img src="data:image/png;base64,{{ $qr }}" alt="Código QR">
        </div>    
    </div>
</body>
</html>
