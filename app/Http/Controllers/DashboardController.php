<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {

        $usuarioInterno = Session::get('usuario_interno');
       if (!$usuarioInterno->rol || $usuarioInterno->rol->clave !== 'ADMIN') {
            return redirect()->route('navbar')->with('error', 'No tienes el permiso para acceder.');
        }       


        $totales = DB::select('
            SELECT SUM(cnt) AS sumatotal FROM (
            SELECT et.tipo, COUNT(*) AS cnt
            FROM tramite_estado_tramite tet
            JOIN estado_tramite et ON tet.id_estado_tramite = et.id_estado_tramite
            WHERE tet.activo = 1
            AND et.tipo IN (\'EN_CREACION\',\'PERSONALIZADO\', \'EXPEDIENTE\', \'INICIADO\',\'A_FINALIZAR\',\'DE_FINALIZACION\')
            GROUP BY et.tipo
            ) AS subquery;
        ');
        $data = DB::select('
            SELECT et.tipo, COUNT(*) AS total
            FROM tramite_estado_tramite tet
            JOIN estado_tramite et ON tet.id_estado_tramite = et.id_estado_tramite
            WHERE tet.activo = 1
            AND et.tipo IN (\'EN_CREACION\',\'PERSONALIZADO\', \'EXPEDIENTE\', \'INICIADO\',\'A_FINALIZAR\',\'DE_FINALIZACION\')
            GROUP BY et.tipo;
        ');

        $chartData = [];
        foreach ($data as $row) {
            $chartData[] = [
                'tipo' => $row->tipo,
                'total' => $row->total,
            ];
        }

        return view('dashboard.index', compact('chartData','totales'));
    }
}
