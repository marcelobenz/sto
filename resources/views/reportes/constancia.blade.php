<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Constancia PDF</title>
        <style>
            @page {
                margin: 0cm;
            }

            body {
                margin: 0cm;
                padding: 2cm; /* or adjust as needed */
                font-family: Arial, sans-serif;
                font-size: 14px;
            }

            .container {
                width: 100%;
                padding: 0; /* Remove container padding since body now has padding */
            }

            .header {
                text-align: center;
                font-weight: bold;
                font-size: 20px;
            }

            .sub-header {
                text-align: center;
                font-size: 16px;
                margin-bottom: 10px;
            }

            .section-title {
                font-weight: bold;
                font-size: 16px;
                margin-top: 20px;
                border-bottom: 1px solid black;
                padding-bottom: 5px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            th, td {
                border: 1px solid black;
                padding: 8px;
                text-align: left;
                vertical-align: top;
            }

            .qr-container {
                text-align: center;
                margin-top: 20px;
            }

            .footer {
                margin-top: 30px;
                text-align: center;
                font-size: 12px;
            }
        </style>
    </head>
    <body>
        @php
            $seccionesAgrupadas = collect($detalleTramite)->groupBy('titulo');
        @endphp

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
                <tr>
                    <td><strong>CUIT</strong></td>
                    <td>{{ $contribuyenteMultinota->cuit }}</td>
                </tr>
                <tr>
                    <td><strong>Razón Social</strong></td>
                    @if(isset($multinota->solicitante))
                        <td>{{ $contribuyenteMultinota->apellido ?? '---' }}</td>
                    @else
                        <td>{{ $contribuyenteMultinota->apellido . ', ' . $contribuyenteMultinota->nombre ?? '---' }}</td>
                    @endif
                </tr>
            </table>
            @if(isset($multinota->solicitante))
            <div class="section-title">DATOS DEL REPRESENTANTE</div>
            <table>
                <tr>
                    <td><strong>Caracter</strong></td>
                    <td>{{ $tipoCaracter ?? '---' }}</td>
                </tr>
                <tr>
                    <td><strong>Teléfono</strong></td>
                    <td>{{ $multinota->solicitante->telefono ?? '---' }}</td>
                </tr>
                <tr>
                    <td><strong>Nombre / Razón Social</strong></td>
                    <td>{{ $multinota->solicitante->nombre ?? '---' }}</td>
                </tr>
                <tr>
                    <td><strong>Correo</strong></td>
                    <td>{{ $multinota->solicitante->correo ?? '---' }}</td>
                </tr>
            </table>
            @endif
            <div class="section-title">DATOS DEL VEHICULO</div>
            <table>
                <tr>
                    <td><strong>Cuenta</strong></td>
                    <td>{{ $multinota->cuenta ?? '---' }}</td>
                </tr>
                <tr>
                    <td><strong>CUIT</strong></td>
                    <td>{{ $contribuyenteMultinota->cuit ?? '---' }}</td>
                </tr>
                <tr>
                    <td><strong>Nombre del Titular</strong></td>
                    @if(isset($multinota->solicitante))
                        <td>{{ $contribuyenteMultinota->apellido ?? '---' }}</td>
                    @else
                        <td>{{ $contribuyenteMultinota->apellido . ', ' . $contribuyenteMultinota->nombre ?? '---' }}</td>
                    @endif
                </tr>
            </table>
            @foreach($seccionesAgrupadas as $titulo => $campos)
                <div class="section-title">{{ strtoupper($titulo) }}</div>
                <table>
                    @foreach($campos as $campo)
                        <tr>
                            <td><strong>{{ $campo->nombre }}</strong></td>
                            <td>{{ $campo->valor ?? '---' }}</td>
                        </tr>
                    @endforeach
                </table>
            @endforeach
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
