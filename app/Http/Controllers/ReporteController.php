<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class ReporteController extends Controller
{
    public function generarPDF($idTramite)
    {
        $url = url("/tramite/detalle/$idTramite"); // URL que el QR debe redirigir
    
        $datos = [
            'titulo' => 'CONSTANCIA',
            'subtitulo' => 'SOLICITUD DE CONSULTAS/RECLAMOS (09)',
            'fecha_emision' => date('d-m-Y'),
            'id_tramite' => $idTramite,
            'cuit' => '20289064878',
            'razon_social' => 'Dienes, Damian',
            'dominio' => 'MQF823',
            'marca' => 'PEUGEOT',
            'modelo' => '207',
            'anio' => '2013',
            'color' => 'ROJO',
            'motivo' => 'CONSULTAS/RECLAMOS TELEPASE',
            'mensaje' => 'COBRO INDEBIDO DE PEAJE',
            'dni' => '28.906.487',
            'telefono' => '(011) 6129-3929',
            'nacionalidad' => 'ARGENTINA',
            'qr' => base64_encode(QrCode::format('png')->size(150)->generate($url)) // Generar QR en Base64
        ];
    
        $pdf = Pdf::loadView('reportes.constancia', $datos);
        return $pdf->stream("constancia_$idTramite.pdf");
    }

}
