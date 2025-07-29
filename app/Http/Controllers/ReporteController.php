<?php

namespace App\Http\Controllers;

use App\Enums\TipoCaracterEnum;
use App\Models\ContribuyenteMultinota;
use App\Models\Multinota;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReporteController extends Controller
{
    public function generarPDF($idTramite)
    {
        // Obtener información del trámite desde la base de datos
        $detalleTramite = DB::table('multinota_seccion_valor as ms')
            ->join('seccion as s', 'ms.id_seccion', '=', 's.id_seccion')
            ->join('campo as c', 'ms.id_campo', '=', 'c.id_campo')
            ->select('ms.id_multinota_seccion_valor', 's.titulo', 'c.nombre', 'ms.valor')
            ->where('ms.id_tramite', $idTramite)
            ->orderBy('ms.id_multinota_seccion_valor', 'asc')
            ->get();

        $tramiteInfo = DB::table('multinota as m')
            ->where('m.id_tramite', $idTramite)
            ->join('tipo_tramite_multinota as ttm', 'm.id_tipo_tramite_multinota', '=', 'ttm.id_tipo_tramite_multinota')
            ->select('ttm.nombre', 'm.fecha_alta')
            ->first();

        $multinota = Multinota::where('id_tramite', $idTramite)->first();

        $contribuyenteMultinota = ContribuyenteMultinota::where('cuit', $multinota->cuit_contribuyente)->first();

        if (isset($multinota->r_caracter)) {
            // Se obtiene el label del tipo caracter
            $obj = TipoCaracterEnum::from($multinota->r_caracter);
            $tipoCaracter = $obj->descripcion();
        }

        // Si no hay datos, asignar valores por defecto
        if (! $tramiteInfo || ! $detalleTramite) {
            abort(404, 'No se encontraron datos para este trámite.');
        }

        // Generar URL para el QR
        $url = url("/tramites/$idTramite/detalle");

        // Generar código QR en Base64
        $qr = base64_encode(QrCode::format('png')->size(150)->generate($url));

        // Pasar datos a la vista
        /*$datos = [
            'titulo' => 'CONSTANCIA',
            'subtitulo' => 'SOLICITUD DE CONSULTAS/RECLAMOS (09)',
            'fecha_emision' => date('d-m-Y', strtotime($tramiteInfo->fecha_emision)),
            'id_tramite' => $idTramite,
            'cuit' => $detalleTramite->cuit,
            'razon_social' => $detalleTramite->razon_social,
            'dni' => $detalleTramite->dni,
            'telefono' => $detalleTramite->telefono,
            'nacionalidad' => $detalleTramite->nacionalidad,
            'dominio' => $detalleTramite->dominio,
            'marca' => $detalleTramite->marca,
            'modelo' => $detalleTramite->modelo,
            'anio' => $detalleTramite->anio,
            'color' => $detalleTramite->color,
            'motivo' => $tramiteInfo->motivo,
            'mensaje' => 'COBRO INDEBIDO DE PEAJE', // Esto puede venir de la BD si es dinámico
            'qr' => $qr
        ];*/

        // Generar el PDF
        $pdf = Pdf::loadView('reportes.constancia', [
            'idTramite' => $idTramite,
            'detalleTramite' => $detalleTramite, // Mantenerlo como colección
            'tramiteInfo' => $tramiteInfo,
            'multinota' => $multinota,
            'contribuyenteMultinota' => $contribuyenteMultinota,
            'tipoCaracter' => $tipoCaracter ?? null,
            'qr' => $qr,
        ]);

        return $pdf->stream("constancia_$idTramite.pdf"); // Para visualizar en el navegador
    }
}
