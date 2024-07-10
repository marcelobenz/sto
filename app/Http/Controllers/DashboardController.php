<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $data = DB::select('
            SELECT et.tipo, COUNT(*) AS total
            FROM tramite_estado_tramite tet
            JOIN estado_tramite et ON tet.id_estado_tramite = et.id_estado_tramite
            WHERE tet.activo = 1
            AND et.tipo IN (\'PERSONALIZADO\', \'EXPEDIENTE\', \'INICIADO\')
            GROUP BY et.tipo;
        ');

        $chartData = [];
        foreach ($data as $row) {
            $chartData[] = [
                'tipo' => $row->tipo,
                'total' => $row->total,
            ];
        }

        return view('dashboard.index', compact('chartData'));
    }
}
